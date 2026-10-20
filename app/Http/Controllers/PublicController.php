<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;

class PublicController extends Controller
{
    /**
     * Página pública de una empresa con todos sus productos activos.
     * Accesible sin autenticación.
     */
    public function company(Company $company)
    {
        // Solo mostrar empresas activas
        abort_if($company->status !== 'active', 404);

        $products = Product::where('company_id', $company->id)
            ->where('active', true)
            ->latest()
            ->paginate(12);

        return view('public.company', compact('company', 'products'));
    }

    /**
     * API: datos rápidos de empresa para el tooltip hover (JSON).
     */
    public function companyTooltip(Company $company)
    {
        abort_if($company->status !== 'active', 404);

        return response()->json([
            'name'        => $company->name,
            'category'    => $company->category,
            'description' => $company->description,
            'phone'       => $company->phone,
            'email'       => $company->email,
            'address'     => $company->address,
            'instagram'   => $company->instagram,
            'facebook'    => $company->facebook,
            'website'     => $company->website,
            'logo'        => $company->logo ? asset('storage/' . $company->logo) : null,
            'products_count' => Product::where('company_id', $company->id)->where('active', true)->count(),
        ]);
    }

    /**
     * Página de detalle de producto con sección de calificaciones y reseñas.
     */
    public function product(Product $product)
    {
        abort_if(!$product->active || ($product->company && $product->company->status !== 'active'), 404);

        $product->load(['company', 'reviews.user']);

        $userHasPurchased = false;
        $userReview = null;

        if (auth()->check()) {
            $userHasPurchased = \App\Models\Order::where('user_id', auth()->id())
                ->where('status', '!=', 'cancelled')
                ->whereHas('details', fn($q) => $q->where('product_id', $product->id))
                ->exists();

            $userReview = $product->reviews->where('user_id', auth()->id())->first();
        }

        $relatedProducts = Product::where('company_id', $product->company_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->take(4)
            ->get();

        return view('public.product', compact('product', 'userHasPurchased', 'userReview', 'relatedProducts'));
    }

    /**
     * Registrar o actualizar reseña y calificación del producto.
     */
    public function storeReview(\Illuminate\Http\Request $request, Product $product)
    {
        abort_if(!$product->active, 404);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ], [
            'rating.required'  => 'Por favor selecciona una calificación de 1 a 5 estrellas.',
            'rating.min'       => 'La calificación mínima es 1 estrella.',
            'rating.max'       => 'La calificación máxima son 5 estrellas.',
            'comment.required' => 'Por favor escribe tu opinión o reseña sobre el producto.',
            'comment.min'      => 'La reseña debe tener al menos 3 caracteres.',
            'comment.max'      => 'La reseña no puede exceder los 1000 caracteres.',
        ]);

        $isVerified = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->whereHas('details', fn($q) => $q->where('product_id', $product->id))
            ->exists();

        \App\Models\Review::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id'    => auth()->id(),
            ],
            [
                'rating'               => (int)$request->rating,
                'comment'              => trim($request->comment),
                'is_verified_purchase' => $isVerified,
            ]
        );

        return redirect()->route('public.product', $product)
            ->with('success', '¡Muchas gracias! Tu reseña y calificación han sido publicadas con éxito.');
    }

    /**
     * Portal público de rastreo en tiempo real mediante número de guía.
     */
    public function trackingPortal(\Illuminate\Http\Request $request, \App\Services\Shipping\ShipmentTrackingService $trackingService)
    {
        $trackingNumber = trim((string)$request->input('guia', ''));
        $shipment = null;
        $error = null;

        if (!empty($trackingNumber)) {
            $shipment = \App\Models\Shipment::where('tracking_number', $trackingNumber)
                ->with(['company', 'order.details.product', 'events'])
                ->first();

            if (!$shipment) {
                $error = "No se encontró ningún paquete con la guía '{$trackingNumber}'. Por favor verifica el número e intenta nuevamente.";
            }
        }

        return view('public.tracking', compact('trackingNumber', 'shipment', 'error'));
    }
}
