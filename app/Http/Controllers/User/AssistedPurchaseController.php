<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{AssistedPurchase, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AssistedPurchaseController extends Controller
{
    /**
     * Listado de solicitudes de compra internacional del usuario comprador.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');

        $requests = AssistedPurchase::where('user_id', Auth::id())
            ->with(['company', 'product'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = AssistedPurchase::getStatuses();

        return view('user.assisted_purchases.index', compact('requests', 'statuses', 'status'));
    }

    /**
     * Formulario para solicitar compra asistida internacional de un producto.
     */
    public function create(Product $product)
    {
        abort_if(!$product->active, 404, 'Producto no disponible.');

        $user = Auth::user();
        $buyerCountry = $user->country ?: 'CO';
        $sellerCountry = $product->company->country ?: 'CO';

        $preloaded = [
            'recipient_name'  => $user->name,
            'recipient_email' => $user->email,
            'recipient_phone' => $user->phone ?? '',
            'destination_country' => $buyerCountry,
            'destination_state'   => $user->state ?? '',
            'destination_city'    => $user->city ?? '',
            'destination_address' => $user->address ?? '',
        ];

        return view('user.assisted_purchases.create', compact('product', 'preloaded', 'sellerCountry', 'buyerCountry'));
    }

    /**
     * Guarda la nueva solicitud internacional asistida.
     */
    public function store(Request $request, Product $product)
    {
        abort_if(!$product->active, 404, 'Producto no disponible.');

        $request->validate([
            'quantity'                => 'required|integer|min:1|max:' . max(1, $product->stock),
            'destination_country'     => 'required|in:CO,MX',
            'destination_state'       => 'required|string|max:100',
            'destination_city'        => 'required|string|max:100',
            'destination_address'     => 'required|string|max:255',
            'destination_postal_code' => 'nullable|string|max:20',
            'recipient_name'          => 'required|string|max:150',
            'recipient_phone'         => 'required|string|max:50',
            'recipient_email'         => 'required|email|max:150',
            'buyer_notes'             => 'nullable|string|max:1000',
        ], [
            'quantity.required'            => 'La cantidad solicitada es obligatoria.',
            'destination_country.required' => 'Debe indicar el país de destino.',
            'destination_state.required'   => 'El departamento o estado de destino es obligatorio.',
            'destination_city.required'    => 'La ciudad o municipio de entrega es obligatoria.',
            'destination_address.required' => 'La dirección física completa de entrega es obligatoria.',
            'recipient_name.required'      => 'El nombre de quien recibe el envío es obligatorio.',
            'recipient_phone.required'     => 'El teléfono de contacto en destino es obligatorio.',
            'recipient_email.required'     => 'El correo electrónico para notificaciones es obligatorio.',
        ]);

        $company = $product->company;
        $originCountry = $company->country ?: 'CO';
        $currency = $product->currency ?: ($company->currency ?: ($originCountry === 'MX' ? 'MXN' : 'COP'));
        $originalPrice = (float) $product->price;
        $quantity = (int) $request->quantity;
        $initialTotal = round($originalPrice * $quantity, 2);

        $requestCode = 'SPOT-INT-' . strtoupper(Str::random(6));

        $assistedPurchase = AssistedPurchase::create([
            'request_code'            => $requestCode,
            'user_id'                 => Auth::id(),
            'company_id'              => $company->id,
            'product_id'              => $product->id,
            'quantity'                => $quantity,
            'origin_country'          => $originCountry,
            'destination_country'     => $request->destination_country,
            'destination_state'       => $request->destination_state,
            'destination_city'        => $request->destination_city,
            'destination_address'     => $request->destination_address,
            'destination_postal_code' => $request->destination_postal_code,
            'recipient_name'          => $request->recipient_name,
            'recipient_phone'         => $request->recipient_phone,
            'recipient_email'         => $request->recipient_email,
            // Instantánea inmutable del precio base original
            'original_price'          => $originalPrice,
            'original_currency'       => $currency,
            'shipping_cost_agreed'    => 0.00,
            'other_fees_agreed'       => 0.00,
            'total_agreed'            => $initialTotal,
            'payment_currency'        => $currency,
            'payment_method_agreed'   => 'Por coordinar con vendedor',
            'status'                  => AssistedPurchase::STATUS_PENDING_REVIEW,
            'buyer_notes'             => $request->buyer_notes,
        ]);

        return redirect()->route('user.assisted_purchases.show', $assistedPurchase)
            ->with('success', "Solicitud internacional registrada con código {$requestCode}. El comercio revisará los costos de envío y condiciones de entrega.");
    }

    /**
     * Ver el detalle y seguimiento de una solicitud asistida.
     */
    public function show(AssistedPurchase $assistedPurchase)
    {
        // Autorización: solo el comprador propietario o admin
        if ($assistedPurchase->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'No tiene autorización para ver esta solicitud.');
        }

        $assistedPurchase->load(['company', 'product', 'user']);
        $statuses = AssistedPurchase::getStatuses();

        return view('user.assisted_purchases.show', compact('assistedPurchase', 'statuses'));
    }

    /**
     * El comprador registra referencia o comprobante de pago acordado.
     */
    public function recordPayment(Request $request, AssistedPurchase $assistedPurchase)
    {
        if ($assistedPurchase->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($assistedPurchase->status, [AssistedPurchase::STATUS_PENDING_PAYMENT, AssistedPurchase::STATUS_IN_NEGOTIATION])) {
            return back()->with('error', 'La solicitud no se encuentra en estado de pago pendiente.');
        }

        $request->validate([
            'payment_reference' => 'required|string|max:100',
            'payment_notes'     => 'nullable|string|max:500',
        ], [
            'payment_reference.required' => 'La referencia del pago o transacción es obligatoria.',
        ]);

        $assistedPurchase->update([
            'payment_reference' => $request->payment_reference,
            'buyer_notes'       => trim(($assistedPurchase->buyer_notes ? $assistedPurchase->buyer_notes . "\n" : '') . "[Comprobante]: " . $request->payment_reference . " - " . $request->payment_notes),
            'status'            => AssistedPurchase::STATUS_PAYMENT_PROCESSING,
        ]);

        return back()->with('success', 'Referencia de pago enviada. El comercio verificará el abono para iniciar la preparación del envío.');
    }

    /**
     * El comprador cancela la solicitud si no ha sido despachada.
     */
    public function cancel(AssistedPurchase $assistedPurchase)
    {
        if ($assistedPurchase->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($assistedPurchase->status, [AssistedPurchase::STATUS_SHIPPED, AssistedPurchase::STATUS_DELIVERED])) {
            return back()->with('error', 'No es posible cancelar una solicitud que ya se encuentra en camino o entregada.');
        }

        $assistedPurchase->update([
            'status' => AssistedPurchase::STATUS_CANCELLED,
        ]);

        return back()->with('success', 'La solicitud internacional ha sido cancelada.');
    }
}
