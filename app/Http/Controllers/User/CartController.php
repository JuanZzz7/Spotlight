<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Product, Order, OrderDetail};
use App\Services\Shipping\ShipmentTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    public function index()
    {
        $cart  = $this->getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $qty;
                $total   += $subtotal;
                $items[]  = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
            }
        }

        return view('user.cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = $this->getCart();
        $targetCountry = $product->country ?: ($product->company?->country ?: 'CO');

        // Salvaguarda: No mezclar productos de países distintos en el mismo carrito local
        if (!empty($cart)) {
            $existingProduct = Product::with('company')->whereIn('id', array_keys($cart))->first();
            if ($existingProduct) {
                $existingCountry = $existingProduct->country ?: ($existingProduct->company?->country ?: 'CO');
                if ($existingCountry !== $targetCountry) {
                    $origName = $targetCountry === 'MX' ? 'México' : 'Colombia';
                    $existName = $existingCountry === 'MX' ? 'México' : 'Colombia';
                    $errorMsg = "Tu carrito contiene productos locales de {$existName}. No es posible mezclar en una misma compra directa productos de {$origName} debido a diferencias de moneda, impuestos y aduanas. Puedes adquirir este producto mediante 'Compra Internacional Asistida' o finalizar primero tu carrito actual.";

                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success'  => false,
                            'error'    => $errorMsg,
                            'conflict' => true,
                        ], 422);
                    }
                    return back()->with('error', $errorMsg);
                }
            }
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:100'
        ]);

        $qty  = (int) ($validated['quantity'] ?? 1);
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '¡' . $product->name . ' agregado al carrito!',
                'cart_count' => array_sum($cart),
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function remove(Product $product)
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        session(['cart' => $cart]);
        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:100'
        ]);

        $cart = $this->getCart();
        $qty  = (int) $validated['quantity'];
        if ($qty <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $qty;
        }
        session(['cart' => $cart]);
        return back();
    }

    public function checkout()
    {
        $cart = $this->getCart();
        if (empty($cart)) return redirect()->route('user.cart.index')->with('error', 'El carrito está vacío.');

        $items = [];
        $total = 0;
        foreach ($cart as $productId => $qty) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->price * $qty;
                $total   += $subtotal;
                $items[]  = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
            }
        }

        return view('user.cart.checkout', compact('items', 'total'));
    }

    public function processPayment(Request $request, ShipmentTrackingService $trackingService)
    {
        $request->validate([
            'card_name'   => 'required|string|max:100',
            'card_number' => 'required|string|max:25',
            'exp_date'    => 'required|string|max:10',
            'cvv'         => 'required|string|max:4',
            'address'     => 'required|string|max:255',
            'payment_method_code' => 'nullable|string|in:10,48,49,31,1',
        ]);

        $cart = $this->getCart();
        if (empty($cart)) return redirect()->route('user.cart.index');

        // Agrupación multi-vendedor: cada empresa vendedora genera un pedido independiente
        // para garantizar la separación fiscal y evitar facturación indebida a nombre de terceros.
        $vendorItems = [];

        foreach ($cart as $productId => $qty) {
            $product = Product::with('company')->find($productId);
            if ($product && $product->stock >= $qty) {
                $companyId = $product->company_id;
                $vendorItems[$companyId][] = [
                    'product'  => $product,
                    'quantity' => $qty,
                    'subtotal' => $product->price * $qty,
                ];
                $product->decrement('stock', $qty);
            }
        }

        if (empty($vendorItems)) {
            return back()->with('error', 'No se pudo procesar ningún producto (sin existencias disponibles).');
        }

        $paymentMethod = $request->input('payment_method_code', '48'); // 48: Tarjeta de crédito simulada
        $createdOrdersCount = 0;

        foreach ($vendorItems as $companyId => $items) {
            $companyTotal = array_sum(array_column($items, 'subtotal'));

            // Crear orden independiente para este comercio
            $order = Order::create([
                'user_id'             => Auth::id(),
                'company_id'          => $companyId,
                'total'               => $companyTotal,
                'status'              => 'paid', // Estado comercial del pedido
                'billing_status'      => 'not_billed', // Facturación electrónica separada
                'payment_method_code' => $paymentMethod,
            ]);

            foreach ($items as $item) {
                $p = $item['product'];
                $lineSubtotal = $item['subtotal'];
                $taxRate = (float) ($p->tax_rate ?? 19.00);
                $taxAmount = round($lineSubtotal * ($taxRate / 100), 2);

                OrderDetail::create([
                    'order_id'        => $order->id,
                    'product_id'      => $p->id,
                    'company_id'      => $companyId,
                    'product_name'    => $p->name, // Snapshot histórico inmutable
                    'product_sku'     => $p->sku ?: 'SKU-' . $p->id,
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $p->price,
                    'subtotal'        => $lineSubtotal,
                    'tax_type'        => $p->tax_type ?: '01',
                    'tax_rate'        => $taxRate,
                    'tax_amount'      => $taxAmount,
                    'discount_amount' => 0.00,
                ]);
            }

            // Inicializar automáticamente el registro de rastreo del envío para este pedido
            $trackingService->createShipmentForOrder($order, [
                'address' => $request->address,
                'carrier' => 'coordinadora',
            ]);

            $createdOrdersCount++;
        }

        session()->forget('cart');

        $message = $createdOrdersCount > 1
            ? "¡Compra realizada con éxito! Se han generado {$createdOrdersCount} pedidos separados según cada comercio vendedor."
            : '¡Pago registrado y compra realizada con éxito!';

        return redirect()->route('user.orders.index')->with('success', $message);
    }
}
