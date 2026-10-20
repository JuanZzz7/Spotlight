<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class BillingDashboardController extends Controller
{
    public function index()
    {
        /** @var Company $company */
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }

        if ($company->isInformal()) {
            return view('business.billing.informal_notice', compact('company'));
        }

        $profile = $company->fiscalProfile;
        $config = $company->billingConfiguration;

        // Estadísticas de documentos
        $totalInvoiced = $company->electronicDocuments()
            ->where('document_type', ElectronicDocument::TYPE_INVOICE)
            ->where('status', ElectronicDocument::STATUS_VALIDATED)
            ->sum('payable_amount');

        $totalTax = $company->electronicDocuments()
            ->where('document_type', ElectronicDocument::TYPE_INVOICE)
            ->where('status', ElectronicDocument::STATUS_VALIDATED)
            ->sum('tax_total_amount');

        $totalDocuments = $company->electronicDocuments()->count();

        $pendingSalesCount = Order::where(function ($q) use ($company) {
                $q->where('company_id', $company->id)
                  ->orWhereHas('details', function ($d) use ($company) {
                      $d->where('company_id', $company->id);
                  });
            })
            ->where('billing_status', 'not_billed')
            ->count();

        $recentDocuments = $company->electronicDocuments()
            ->with(['order', 'items'])
            ->latest()
            ->take(5)
            ->get();

        return view('business.billing.dashboard', compact(
            'company',
            'profile',
            'config',
            'totalInvoiced',
            'totalTax',
            'totalDocuments',
            'pendingSalesCount',
            'recentDocuments'
        ));
    }
}
