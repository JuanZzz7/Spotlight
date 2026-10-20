<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\{AssistedPurchase, Company};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistedPurchaseController extends Controller
{
    private function getCompany(): Company
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }
        return $company;
    }

    /**
     * Listado de solicitudes internacionales recibidas por el comercio.
     */
    public function index(Request $request)
    {
        $company = $this->getCompany();
        $status = $request->get('status');

        $requests = AssistedPurchase::where('company_id', $company->id)
            ->with(['user', 'product'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statuses = AssistedPurchase::getStatuses();

        $stats = [
            'total'     => AssistedPurchase::where('company_id', $company->id)->count(),
            'pending'   => AssistedPurchase::where('company_id', $company->id)->whereIn('status', [AssistedPurchase::STATUS_PENDING_REVIEW, AssistedPurchase::STATUS_IN_NEGOTIATION])->count(),
            'paid'      => AssistedPurchase::where('company_id', $company->id)->where('status', AssistedPurchase::STATUS_PAID)->count(),
            'completed' => AssistedPurchase::where('company_id', $company->id)->where('status', AssistedPurchase::STATUS_DELIVERED)->count(),
        ];

        return view('business.assisted_purchases.index', compact('company', 'requests', 'statuses', 'status', 'stats'));
    }

    /**
     * Detalle y panel de respuesta/gestión para una solicitud internacional.
     */
    public function show(AssistedPurchase $assistedPurchase)
    {
        $company = $this->getCompany();

        if ($assistedPurchase->company_id !== $company->id) {
            abort(403, 'Esta solicitud no corresponde a su comercio.');
        }

        $assistedPurchase->load(['user', 'product']);
        $statuses = AssistedPurchase::getStatuses();

        return view('business.assisted_purchases.show', compact('company', 'assistedPurchase', 'statuses'));
    }

    /**
     * El comerciante actualiza las condiciones acordadas (costo de envío, método, tiempos) y el estado.
     */
    public function updateConditions(Request $request, AssistedPurchase $assistedPurchase)
    {
        $company = $this->getCompany();

        if ($assistedPurchase->company_id !== $company->id) {
            abort(403, 'Esta solicitud no corresponde a su comercio.');
        }

        $request->validate([
            'status'                  => 'required|in:' . implode(',', array_keys(AssistedPurchase::getStatuses())),
            'shipping_cost_agreed'    => 'nullable|numeric|min:0',
            'other_fees_agreed'       => 'nullable|numeric|min:0',
            'payment_currency'        => 'required|in:COP,MXN,USD',
            'payment_method_agreed'   => 'nullable|string|max:100',
            'estimated_delivery_days' => 'nullable|string|max:50',
            'carrier_name'            => 'nullable|string|max:100',
            'tracking_number'         => 'nullable|string|max:100',
            'seller_notes'            => 'nullable|string|max:1500',
        ]);

        $shipping = (float) $request->input('shipping_cost_agreed', 0);
        $otherFees = (float) $request->input('other_fees_agreed', 0);
        $subtotal = round((float) $assistedPurchase->original_price * (int) $assistedPurchase->quantity, 2);
        $totalAgreed = round($subtotal + $shipping + $otherFees, 2);

        $assistedPurchase->update([
            'status'                  => $request->status,
            'shipping_cost_agreed'    => $shipping,
            'other_fees_agreed'       => $otherFees,
            'total_agreed'            => $totalAgreed,
            'payment_currency'        => $request->payment_currency,
            'payment_method_agreed'   => $request->payment_method_agreed ?: 'Coordinación directa con vendedor',
            'estimated_delivery_days' => $request->estimated_delivery_days,
            'carrier_name'            => $request->carrier_name,
            'tracking_number'         => $request->tracking_number,
            'seller_notes'            => $request->seller_notes,
        ]);

        return redirect()->route('business.assisted_purchases.show', $assistedPurchase)
            ->with('success', 'Condiciones de la compra internacional actualizadas con éxito.');
    }

    /**
     * El comerciante confirma haber recibido el pago acordado.
     */
    public function confirmPayment(Request $request, AssistedPurchase $assistedPurchase)
    {
        $company = $this->getCompany();

        if ($assistedPurchase->company_id !== $company->id) {
            abort(403, 'Esta solicitud no corresponde a su comercio.');
        }

        $assistedPurchase->update([
            'status'  => AssistedPurchase::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return redirect()->route('business.assisted_purchases.show', $assistedPurchase)
            ->with('success', 'Pago verificado y confirmado. La solicitud se encuentra ahora en estado "Pagada".');
    }
}
