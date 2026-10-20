<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ElectronicDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingReportController extends Controller
{
    private function getCompany(): Company
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }
        return $company;
    }

    public function index(Request $request)
    {
        $company = $this->getCompany();

        $year = (int) $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $query = $company->electronicDocuments()
            ->where('status', ElectronicDocument::STATUS_VALIDATED)
            ->whereYear('issue_date', $year);

        if ($month && $month !== 'all') {
            $query->whereMonth('issue_date', (int) $month);
        }

        $documents = (clone $query)->with('items')->latest('issue_date')->get();

        // Cálculos discriminados por impuestos y tipo de documento
        $totalInvoicedGross = 0.00;
        $totalCreditNotes = 0.00;
        $taxBaseTotal = 0.00;
        $iva19Total = 0.00;
        $iva5Total = 0.00;
        $iva0Total = 0.00;

        foreach ($documents as $doc) {
            if ($doc->isInvoice()) {
                $totalInvoicedGross += (float) $doc->payable_amount;
                $taxBaseTotal += (float) $doc->tax_exclusive_amount;

                foreach ($doc->items as $it) {
                    $rate = (float) $it->tax_rate;
                    if ($rate >= 18) {
                        $iva19Total += (float) $it->tax_amount;
                    } elseif ($rate >= 4) {
                        $iva5Total += (float) $it->tax_amount;
                    } else {
                        $iva0Total += (float) $it->line_extension_amount;
                    }
                }
            } elseif ($doc->isCreditNote()) {
                $totalCreditNotes += (float) $doc->payable_amount;
            }
        }

        $totalNet = $totalInvoicedGross - $totalCreditNotes;

        // Exportación a CSV
        if ($request->input('export') === 'csv') {
            $filename = "reporte_fiscal_{$company->id}_{$year}_{$month}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($documents) {
                $handle = fopen('php://output', 'w');
                // BOM para UTF-8 en Excel
                fputs($handle, "\xEF\xBB\xBF");
                fputcsv($handle, ['Tipo Documento', 'Número', 'Fecha', 'NIT/Cédula Adquirente', 'Cliente', 'Base Gravable', 'Total IVA', 'Total Documento', 'CUFE', 'Estado DIAN']);

                foreach ($documents as $doc) {
                    fputcsv($handle, [
                        $doc->type_label,
                        $doc->full_number,
                        $doc->issue_date->format('Y-m-d'),
                        $doc->customer_id_number,
                        $doc->customer_name,
                        number_format((float) $doc->tax_exclusive_amount, 2, '.', ''),
                        number_format((float) $doc->tax_total_amount, 2, '.', ''),
                        number_format((float) $doc->payable_amount, 2, '.', ''),
                        $doc->uuid_cufe,
                        $doc->dian_status,
                    ]);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('business.billing.reports.index', compact(
            'company',
            'year',
            'month',
            'documents',
            'totalInvoicedGross',
            'totalCreditNotes',
            'taxBaseTotal',
            'iva19Total',
            'iva5Total',
            'iva0Total',
            'totalNet'
        ));
    }
}
