<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function company()
    {
        return Auth::user()->company;
    }

    public function index(Request $request)
    {
        $company = $this->company();
        if (!$company) {
            return redirect()->route('business.company.edit')->with('error', 'Debes registrar tu empresa primero.');
        }

        $status = $request->get('status', 'all');
        $search = $request->get('q', $request->get('search', ''));
        $sort   = $request->get('sort', 'recent');

        $query = $company->products();

        // Filtro por término de búsqueda (nombre, sku, descripción)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por Estado
        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }

        // Ordenamiento
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }

        // Conteos globales
        $allProductsCount = $company->products()->count();
        $activeCount      = $company->products()->where('active', true)->count();
        $outOfStockCount  = $company->products()->where('stock', '<=', 0)->count();

        // Métricas de inventario
        $totalStockValue   = $company->products()->get()->sum(fn($p) => $p->price * $p->stock);
        $totalUnitsInStock = $company->products()->sum('stock');
        $featuredCount     = $company->products()->where('featured', true)->count();

        $products = $query->paginate(12)->withQueryString();

        return view('business.products.index', compact(
            'products',
            'company',
            'status',
            'search',
            'sort',
            'allProductsCount',
            'activeCount',
            'outOfStockCount',
            'totalStockValue',
            'totalUnitsInStock',
            'featuredCount'
        ));
    }

    public function exportPdf(Request $request)
    {
        $company = $this->company();
        if (!$company) {
            return redirect()->route('business.company.edit')->with('error', 'Debes registrar tu empresa primero.');
        }

        $status = $request->get('status', 'all');
        $query  = $company->products();

        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'out_of_stock') {
            $query->where('stock', '<=', 0);
        }

        $products   = $query->orderBy('name', 'asc')->get();
        $totalUnits = $products->sum('stock');
        $totalValue = $products->sum(fn($p) => $p->price * $p->stock);

        return view('business.products.inventory_pdf', compact('company', 'products', 'totalUnits', 'totalValue', 'status'));
    }

    public function create()
    {
        return view('business.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'tax_type'    => 'nullable|string|max:10',
            'unit_measure'=> 'nullable|string|max:10',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'active'      => 'nullable|boolean',
            'featured'    => 'nullable|boolean',
        ], [
            'image.max'   => 'La imagen del producto no puede superar los 2 MB por capacidad del sistema.',
            'image.image' => 'El archivo debe ser una imagen válida (JPG, PNG, WebP).',
        ]);

        $company = $this->company();
        if (!$company) {
            return redirect()->route('business.company.edit')->with('error', 'Debes crear tu empresa primero.');
        }

        $data = $request->except(['image', '_token']);
        $data['active']   = $request->boolean('active', true);
        $data['featured'] = $request->boolean('featured', false);
        $data['country']  = $company->country ?: 'CO';
        $data['currency'] = $company->currency ?: ($company->isMexican() ? 'MXN' : 'COP');
        $defaultTax       = $company->isMexican() ? 16.00 : 19.00;
        $data['tax_rate'] = $request->input('tax_rate', $defaultTax);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            // Si el comerciante no quiere subir foto, usar el logo de su empresa
            $data['image'] = $company->logo ?? null;
        }

        $company->products()->create($data);
        return redirect()->route('business.products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        return view('business.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $request->validate([
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'tax_type'    => 'nullable|string|max:10',
            'unit_measure'=> 'nullable|string|max:10',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'image.max'   => 'La imagen del producto no puede superar los 2 MB por capacidad del sistema.',
            'image.image' => 'El archivo debe ser una imagen válida (JPG, PNG, WebP).',
        ]);

        $data = $request->except(['image', '_token', '_method']);
        $data['active']   = $request->boolean('active');
        $data['featured'] = $request->boolean('featured');
        if ($request->has('tax_rate')) {
            $data['tax_rate'] = $request->input('tax_rate');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('business.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        // Protección contra destrucción de trazabilidad contable
        if ($product->orderDetails()->exists()) {
            return back()->with('error', 'No es posible eliminar el producto porque tiene ventas y documentos fiscales asociados. En su lugar, desactívalo desmarcando la casilla "Activo".');
        }

        $product->delete();
        return redirect()->route('business.products.index')->with('success', 'Producto eliminado.');
    }

    private function authorizeProduct(Product $product)
    {
        $company = $this->company();
        if (!$company || $product->company_id !== $company->id) {
            abort(403);
        }
    }
}
