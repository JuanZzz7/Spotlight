<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $company = $this->company();
        if (!$company) {
            return redirect()->route('business.company.edit')->with('error', 'Debes registrar tu empresa primero.');
        }
        $tags = Tag::availableForCompany($company->id)->orderBy('label')->get();
        return view('business.products.create', compact('company', 'tags'));
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
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery'     => 'nullable|array|max:4',
            'gallery.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'active'      => 'nullable|boolean',
            'featured'    => 'nullable|boolean',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
            'new_tag_label' => 'nullable|string|max:100',
            'new_tag_color' => 'nullable|string|max:25',
        ], [
            'image.max'        => 'La imagen principal del producto no puede superar los 5 MB.',
            'image.image'      => 'El archivo principal debe ser una imagen válida (JPG, PNG, WebP).',
            'gallery.max'      => 'Puedes subir como máximo 4 imágenes adicionales para la galería.',
            'gallery.*.max'    => 'Cada imagen de la galería no puede superar los 5 MB.',
            'gallery.*.image'  => 'Los archivos de la galería deben ser imágenes válidas (JPG, PNG, WebP).',
        ]);

        $company = $this->company();
        if (!$company) {
            return redirect()->route('business.company.edit')->with('error', 'Debes crear tu empresa primero.');
        }

        $data = $request->except(['image', 'gallery', '_token', 'tags', 'new_tag_label', 'new_tag_color', 'new_tag_icon']);
        $data['active']   = $request->boolean('active', true);
        $data['featured'] = $request->boolean('featured', false);
        $data['country']  = $company->country ?: 'CO';
        $data['currency'] = $company->currency ?: ($company->isMexican() ? 'MXN' : 'COP');
        $defaultTax       = $company->isMexican() ? 16.00 : 19.00;
        $data['tax_rate'] = $request->input('tax_rate', $defaultTax);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            $data['image'] = null;
        }

        // Procesar imágenes de galería secundaria
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                if ($file && $file->isValid()) {
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
            }
            $data['gallery'] = !empty($galleryPaths) ? $galleryPaths : null;
        } else {
            $data['gallery'] = null;
        }

        $product = $company->products()->create($data);

        // Procesar categorías/etiquetas
        $tagIds = $request->input('tags', []);
        if (!is_array($tagIds)) {
            $tagIds = [];
        }

        if ($request->filled('new_tag_label')) {
            $label = trim($request->input('new_tag_label'));
            $slugBase = Str::slug($label);
            $slug = $slugBase;
            $counter = 1;
            while (Tag::where('name', $slug)->exists()) {
                $slug = $slugBase . '-' . $company->id . ($counter > 1 ? "-{$counter}" : '');
                $counter++;
            }
            $newTag = Tag::create([
                'company_id' => $company->id,
                'name'       => $slug,
                'label'      => $label,
                'icon'       => $request->input('new_tag_icon', 'bi-tag-fill'),
                'theme'      => ['accent' => $request->input('new_tag_color', '#7F1D3F')],
            ]);
            $tagIds[] = $newTag->id;
        }

        if (!empty($tagIds)) {
            $product->tags()->sync(array_unique($tagIds));
        }

        return redirect()->route('business.products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);
        $company = $this->company();
        $tags = Tag::availableForCompany($company?->id)->orderBy('label')->get();
        $product->load('tags');
        return view('business.products.edit', compact('product', 'company', 'tags'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);
        $request->validate([
            'name'                   => 'required|string|max:255',
            'sku'                    => 'nullable|string|max:50',
            'price'                  => 'required|numeric|min:0',
            'cost_price'             => 'nullable|numeric|min:0',
            'tax_rate'               => 'nullable|numeric|min:0|max:100',
            'tax_type'               => 'nullable|string|max:10',
            'unit_measure'           => 'nullable|string|max:10',
            'stock'                  => 'required|integer|min:0',
            'image'                  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery'                => 'nullable|array|max:4',
            'gallery.*'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_gallery_images'  => 'nullable|array',
            'delete_gallery_images.*'=> 'string',
            'tags'                   => 'nullable|array',
            'tags.*'                 => 'exists:tags,id',
            'new_tag_label'          => 'nullable|string|max:100',
            'new_tag_color'          => 'nullable|string|max:25',
        ], [
            'image.max'        => 'La imagen principal del producto no puede superar los 5 MB.',
            'image.image'      => 'El archivo principal debe ser una imagen válida (JPG, PNG, WebP).',
            'gallery.max'      => 'Puedes subir como máximo 4 imágenes adicionales para la galería.',
            'gallery.*.max'    => 'Cada imagen de la galería no puede superar los 5 MB.',
            'gallery.*.image'  => 'Los archivos de la galería deben ser imágenes válidas (JPG, PNG, WebP).',
        ]);

        $data = $request->except(['image', 'gallery', 'delete_gallery_images', '_token', '_method', 'tags', 'new_tag_label', 'new_tag_color', 'new_tag_icon']);
        $data['active']   = $request->boolean('active');
        $data['featured'] = $request->boolean('featured');
        if ($request->has('tax_rate')) {
            $data['tax_rate'] = $request->input('tax_rate');
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Gestión de la galería existente y nuevas imágenes
        $currentGallery = is_array($product->gallery) ? $product->gallery : [];

        // 1. Eliminar imágenes marcadas
        if ($request->has('delete_gallery_images') && is_array($request->input('delete_gallery_images'))) {
            $toDelete = $request->input('delete_gallery_images');
            $remainingGallery = [];
            foreach ($currentGallery as $path) {
                if (in_array($path, $toDelete)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                } else {
                    $remainingGallery[] = $path;
                }
            }
            $currentGallery = $remainingGallery;
        }

        // 2. Agregar nuevas fotos sin exceder 4 imágenes en la galería
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if (count($currentGallery) >= 4) {
                    break;
                }
                if ($file && $file->isValid()) {
                    $currentGallery[] = $file->store('products/gallery', 'public');
                }
            }
        }

        $data['gallery'] = !empty($currentGallery) ? array_values($currentGallery) : null;

        $product->update($data);

        // Procesar categorías/etiquetas
        $tagIds = $request->input('tags', []);
        if (!is_array($tagIds)) {
            $tagIds = [];
        }

        if ($request->filled('new_tag_label')) {
            $company = $this->company();
            $label = trim($request->input('new_tag_label'));
            $slugBase = Str::slug($label);
            $slug = $slugBase;
            $counter = 1;
            while (Tag::where('name', $slug)->exists()) {
                $slug = $slugBase . '-' . ($company ? $company->id : '0') . ($counter > 1 ? "-{$counter}" : '');
                $counter++;
            }
            $newTag = Tag::create([
                'company_id' => $company?->id,
                'name'       => $slug,
                'label'      => $label,
                'icon'       => $request->input('new_tag_icon', 'bi-tag-fill'),
                'theme'      => ['accent' => $request->input('new_tag_color', '#7F1D3F')],
            ]);
            $tagIds[] = $newTag->id;
        }

        $product->tags()->sync(array_unique($tagIds));

        return redirect()->route('business.products.index')->with('success', 'Producto actualizado.');
    }

    public function storeQuickTag(Request $request)
    {
        $company = $this->company();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'No tienes empresa registrada.'], 403);
        }

        $request->validate([
            'label'  => 'required|string|max:100',
            'icon'   => 'nullable|string|max:50',
            'accent' => 'nullable|string|max:25',
        ]);

        $label = trim($request->input('label'));
        $slugBase = Str::slug($label);
        $slug = $slugBase;
        $counter = 1;
        while (Tag::where('name', $slug)->exists()) {
            $slug = $slugBase . '-' . $company->id . ($counter > 1 ? "-{$counter}" : '');
            $counter++;
        }

        $accent = $request->input('accent', '#7F1D3F');
        $icon = $request->input('icon', 'bi-tag-fill');

        $tag = Tag::create([
            'company_id' => $company->id,
            'name'       => $slug,
            'label'      => $label,
            'icon'       => $icon,
            'theme'      => ['accent' => $accent],
        ]);

        return response()->json([
            'success' => true,
            'tag'     => [
                'id'     => $tag->id,
                'label'  => $tag->label,
                'name'   => $tag->name,
                'icon'   => $tag->icon,
                'accent' => $tag->accent_color,
            ],
        ]);
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
