<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Company, Product, Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query     = $request->get('q');
        $category  = $request->get('category');
        $companyId = $request->get('company_id');
        $market    = $request->get('country');
        $sort      = $request->get('sort', 'latest');

        $activeTag = null;
        if ($category) {
            $activeTag = Tag::where('name', $category)->orWhere('label', $category)->first();
        }

        $productsQuery = Product::where('active', true)
            ->with(['company', 'tags'])
            ->when($query, fn($q) => $q->where(function ($sub) use ($query) {
                $sub->where('name', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%");
            }))
            ->when($category, function ($q) use ($category) {
                $q->where(function ($sub) use ($category) {
                    $sub->whereHas('tags', fn($t) => $t->where('name', $category)->orWhere('label', $category))
                        ->orWhereHas('company', fn($c) => $c->where('category', $category));
                });
            })
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->when($market && in_array($market, ['CO', 'MX']), fn($q) => $q->where(function ($sub) use ($market) {
                $sub->where('country', $market)
                    ->orWhereHas('company', fn($c) => $c->where('country', $market));
            }));

        $productsQuery = match ($sort) {
            'price_asc'  => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'name_asc'   => $productsQuery->orderBy('name', 'asc'),
            default      => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(9)->withQueryString();

        $companies = Company::where('status', 'active')
            ->withCount(['products' => fn($q) => $q->where('active', true)])
            ->when($market && in_array($market, ['CO', 'MX']), fn($q) => $q->where('country', $market))
            ->get();

        // Categorías/etiquetas para la exploración del comprador
        $allTags = Tag::whereHas('products', fn($p) => $p->where('active', true))
            ->orWhereNull('company_id')
            ->orderBy('label')
            ->get();

        $categories = $allTags->pluck('label')->unique()->filter();
        if ($categories->isEmpty()) {
            $categories = Company::where('status', 'active')->distinct()->pluck('category')->filter();
        }

        return view('user.dashboard', compact('products', 'companies', 'categories', 'allTags', 'activeTag', 'market', 'sort'));
    }

    public function panel(Request $request)
    {
        $orders = \Illuminate\Support\Facades\Auth::user()
            ->orders()
            ->with(['company', 'shipment.events', 'details.product'])
            ->latest()
            ->get();

        $latestOrder = $orders->first();

        return view('user.panel', compact('orders', 'latestOrder'));
    }

    public function map(Request $request)
    {
        $companies = Company::where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['products' => function($q) {
                $q->where('active', true)->latest();
            }])
            ->get()
            ->map(function ($company) {
                $schedule = $company->schedule_status;
                $rating = $company->average_rating;
                $reviewsCount = $company->reviews_count;

                return [
                    'id'               => $company->id,
                    'user_id'          => $company->user_id,
                    'name'             => $company->name,
                    'category'         => $company->category ?: 'Comercio Local',
                    'description'      => $company->description ?: '',
                    'address'          => $company->address ?: 'Ubicación física',
                    'phone'            => $company->phone ?: '',
                    'email'            => $company->email ?: '',
                    'latitude'         => (float) $company->latitude,
                    'longitude'        => (float) $company->longitude,
                    'logo_url'         => $company->logo_url,
                    'store_url'        => route('public.company', $company->id),
                    'chat_url'         => $company->user_id ? route('chat.show', $company->user_id) : route('chat.index'),
                    'is_open'          => $schedule['is_open'],
                    'schedule_label'   => $schedule['label'],
                    'schedule_details' => $schedule['details'],
                    'schedule_hours'   => $schedule['hours'],
                    'rating'           => $rating > 0 ? $rating : null,
                    'reviews_count'    => $reviewsCount,
                    'products'         => $company->products->map(function ($product) {
                        $formattedPrice = $product->effective_currency === 'MXN'
                            ? $product->formatted_price_cop . ' (🇲🇽 $' . number_format($product->price, 2, ',', '.') . ' MXN)'
                            : '$' . number_format($product->price, 0, ',', '.') . ' COP';

                        return [
                            'id'              => $product->id,
                            'name'            => $product->name,
                            'price'           => (float) $product->price,
                            'formatted_price' => $formattedPrice,
                            'image_url'       => $product->image_url,
                            'stock'           => $product->stock,
                            'add_cart_url'    => route('user.cart.add', $product->id),
                        ];
                    })->values(),
                ];
            });

        $categories = $companies->pluck('category')->unique()->filter()->values();

        return view('user.map', compact('companies', 'categories'));
    }
}
