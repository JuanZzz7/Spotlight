<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistedPurchase;
use Illuminate\Http\Request;

class AssistedPurchaseController extends Controller
{
    /**
     * Supervisión general de solicitudes internacionales entre Colombia y México.
     */
    public function index(Request $request)
    {
        $status      = $request->get('status');
        $origin      = $request->get('origin');
        $destination = $request->get('destination');
        $search      = $request->get('search');

        $requests = AssistedPurchase::with(['company', 'user', 'product'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($origin, fn($q) => $q->where('origin_country', $origin))
            ->when($destination, fn($q) => $q->where('destination_country', $destination))
            ->when($search, function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = AssistedPurchase::getStatuses();

        $stats = [
            'total'          => AssistedPurchase::count(),
            'from_colombia'  => AssistedPurchase::where('origin_country', 'CO')->count(),
            'from_mexico'    => AssistedPurchase::where('origin_country', 'MX')->count(),
            'active_in_flow' => AssistedPurchase::whereNotIn('status', [AssistedPurchase::STATUS_CANCELLED, AssistedPurchase::STATUS_REJECTED, AssistedPurchase::STATUS_DELIVERED])->count(),
        ];

        return view('admin.assisted_purchases.index', compact('requests', 'statuses', 'status', 'origin', 'destination', 'search', 'stats'));
    }

    /**
     * Ver detalle de la solicitud como administrador.
     */
    public function show(AssistedPurchase $assistedPurchase)
    {
        $assistedPurchase->load(['company', 'user', 'product']);
        $statuses = AssistedPurchase::getStatuses();

        return view('admin.assisted_purchases.show', compact('assistedPurchase', 'statuses'));
    }
}
