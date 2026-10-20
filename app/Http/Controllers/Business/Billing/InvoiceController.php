<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ElectronicDocument;
use App\Models\Order;
use App\Models\Product;
use App\Services\Billing\ElectronicDocumentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct(
        protected ElectronicDocumentService $billingService
    ) {}

    private function getCompany(): Company
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }
        return $company;
    }

    /**
     * Listado y panel de ventas/pedidos de la empresa elegibles para facturación.
     */
    public function sales(Request $request)
    {
        $company = $this->getCompany();

        if (!$company->canIssueElectronicInvoices()) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Su comercio no está habilitado para emitir facturación electrónica.');
        }

        $search = $request->get('search');
        $billingStatus = $request->get('billing_status');
        $orderStatus = $request->get('order_status');
        $month = $request->get('month');

        // Base query para las órdenes vinculadas a esta empresa
        $baseQuery = Order::where(function ($q) use ($company) {
            $q->where('company_id', $company->id)
              ->orWhereHas('details', function ($d) use ($company) {
                  $d->where('company_id', $company->id);
              });
        });

        // Métricas financieras y de facturación consolidadas
        $allOrders = (clone $baseQuery)->with(['electronicDocument', 'details'])->get();

        $totalSalesAmount = $allOrders->sum(function ($ord) {
            return $ord->subtotal_company > 0 ? $ord->subtotal_company : $ord->total;
        });

        $totalSettledAmount = $allOrders->where('settlement_status', 'settled')->sum(function ($ord) {
            return $ord->net_company_amount > 0 ? $ord->net_company_amount : $ord->total;
        });

        $billedCount = $allOrders->filter(fn($o) => $o->isBilled())->count();
        $pendingCount = $allOrders->filter(fn($o) => !$o->isBilled())->count();

        $stats = [
            'total_sales'   => $totalSalesAmount,
            'total_settled' => $totalSettledAmount,
            'billed_count'  => $billedCount,
            'pending_count' => $pendingCount,
            'total_orders'  => $allOrders->count(),
        ];

        // Consulta filtrada para la vista
        $query = clone $baseQuery;

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($billingStatus === 'billed') {
            $query->where(function ($q) {
                $q->where('billing_status', 'billed')
                  ->orWhereHas('electronicDocuments', function ($ed) {
                      $ed->where('dian_status', 'validated');
                  });
            });
        } elseif ($billingStatus === 'not_billed') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('billing_status', '!=', 'billed')
                       ->orWhereNull('billing_status');
                })->whereDoesntHave('electronicDocuments', function ($ed) {
                    $ed->where('dian_status', 'validated');
                });
            });
        }

        if ($orderStatus && $orderStatus !== 'all') {
            $query->where(function ($q) use ($orderStatus) {
                $q->where('order_status', $orderStatus)
                  ->orWhere('status', $orderStatus)
                  ->orWhere('payment_status', $orderStatus);
            });
        }

        if ($month) {
            $year = substr($month, 0, 4);
            $monthNum = substr($month, 5, 2);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
        }

        // Exportación de ventas a formato CSV
        if ($request->get('export') === 'csv') {
            $exportOrders = $query->with(['user', 'details.product', 'electronicDocument', 'shipment'])->latest()->get();
            $csvHeader = "ID,Referencia,Fecha,Cliente,Email,Productos,Total Venta,Liquidacion Neta,Estado Pedido,Estado Fiscal,Factura DIAN,CUFE\n";
            $csvRows = "";
            foreach ($exportOrders as $ord) {
                $custName = str_replace('"', '""', $ord->user->name ?? 'Consumidor');
                $custEmail = str_replace('"', '""', $ord->user->email ?? '');
                $prodCount = $ord->details->count();
                $tot = $ord->total;
                $net = $ord->net_company_amount > 0 ? $ord->net_company_amount : $ord->total;
                $st = $ord->order_status ?: $ord->status;
                $fisc = $ord->isBilled() ? 'Facturado' : 'Sin Facturar';
                $factNum = $ord->electronicDocument ? $ord->electronicDocument->full_number : '';
                $cufe = $ord->electronicDocument ? $ord->electronicDocument->cufe : '';
                $csvRows .= "{$ord->id},\"{$ord->reference}\",\"{$ord->created_at->format('Y-m-d H:i')}\",\"{$custName}\",\"{$custEmail}\",{$prodCount},{$tot},{$net},\"{$st}\",\"{$fisc}\",\"{$factNum}\",\"{$cufe}\"\n";
            }
            $fileName = 'ventas_spotlight_' . date('Y-m-d_His') . '.csv';
            return response($csvHeader . $csvRows)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
        }

        $orders = $query
            ->with(['user', 'details.product', 'electronicDocument', 'shipment.events'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('business.billing.invoices.sales', compact('company', 'orders', 'stats', 'search', 'billingStatus', 'orderStatus', 'month'));
    }

    /**
     * Formulario para emitir factura a partir de una orden de venta.
     */
    public function createFromOrder(Order $order)
    {
        $company = $this->getCompany();

        if (!$company->canIssueElectronicInvoices()) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Su comercio no está habilitado para emitir facturación electrónica.');
        }

        // Verificar que la orden pertenezca a la empresa
        $hasCompanyDetails = $order->details()->where(function ($q) use ($company) {
            $q->where('company_id', $company->id)->orWhereNull('company_id');
        })->exists();

        if (!$hasCompanyDetails && $order->company_id !== $company->id) {
            abort(403, 'Esta orden no pertenece a su comercio.');
        }

        if ($order->isBilled()) {
            return redirect()->route('business.billing.sales')
                ->with('error', 'Esta orden ya ha sido facturada electrónicamente.');
        }

        $details = $order->details()
            ->where(function ($q) use ($company) {
                $q->where('company_id', $company->id)->orWhereNull('company_id');
            })
            ->with('product')
            ->get();

        $user = $order->user;

        return view('business.billing.invoices.create_from_order', compact('company', 'order', 'details', 'user'));
    }

    /**
     * Procesa la emisión de factura desde la orden.
     */
    public function storeFromOrder(Request $request, Order $order)
    {
        $company = $this->getCompany();

        $request->validate([
            'customer_name'             => 'required|string|max:255',
            'customer_id_type'          => 'required|string|in:13,31,22,41,42',
            'customer_id_number'        => 'required|string|max:30',
            'customer_email'            => 'nullable|email|max:255',
            'customer_phone'            => 'nullable|string|max:30',
            'customer_address'          => 'nullable|string|max:255',
            'customer_municipality_code'=> 'nullable|string|max:10',
            'payment_method_code'       => 'required|string|in:10,48,49,31,1',
        ]);

        $customerData = [
            'name'              => $request->customer_name,
            'id_type'           => $request->customer_id_type,
            'id_number'         => $request->customer_id_number,
            'dv'                => $request->customer_id_type === '31' ? \App\Models\CompanyFiscalProfile::calculateDv($request->customer_id_number) : null,
            'email'             => $request->customer_email,
            'phone'             => $request->customer_phone,
            'address'           => $request->customer_address,
            'municipality_code' => $request->customer_municipality_code ?: '25843',
        ];

        try {
            $document = $this->billingService->createInvoiceFromOrder(
                $company,
                $order,
                $customerData,
                $request->payment_method_code
            );

            if ($document->isValidated()) {
                return redirect()->route('business.billing.documents.show', $document)
                    ->with('success', "Factura Electrónica {$document->full_number} emitida y validada exitosamente por la DIAN (CUFE generado).");
            }

            return redirect()->route('business.billing.documents.show', $document)
                ->with('warning', "Factura emitida pero requiere atención: {$document->dian_response_message}");
        } catch (Exception $e) {
            return back()->with('error', 'Error al emitir factura electrónica: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para factura electrónica directa.
     */
    public function create()
    {
        $company = $this->getCompany();

        if (!$company->canIssueElectronicInvoices()) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Su comercio no está habilitado para emitir facturación electrónica.');
        }

        $products = $company->products()->where('active', true)->get();

        return view('business.billing.invoices.create_direct', compact('company', 'products'));
    }

    /**
     * Procesa la factura directa.
     */
    public function store(Request $request)
    {
        $company = $this->getCompany();

        $request->validate([
            'customer_name'        => 'required|string|max:255',
            'customer_id_type'     => 'required|string|in:13,31,22,41,42',
            'customer_id_number'   => 'required|string|max:30',
            'customer_email'       => 'nullable|email|max:255',
            'customer_phone'       => 'nullable|string|max:30',
            'customer_address'     => 'nullable|string|max:255',
            'payment_method_code'  => 'required|string|in:10,48,49,31,1',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string|max:255',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.price_amount' => 'required|numeric|min:0',
            'items.*.tax_rate'     => 'required|numeric|in:0,5,19',
        ]);

        $customerData = [
            'name'              => $request->customer_name,
            'id_type'           => $request->customer_id_type,
            'id_number'         => $request->customer_id_number,
            'dv'                => $request->customer_id_type === '31' ? \App\Models\CompanyFiscalProfile::calculateDv($request->customer_id_number) : null,
            'email'             => $request->customer_email,
            'phone'             => $request->customer_phone,
            'address'           => $request->customer_address,
            'municipality_code' => '25843',
        ];

        try {
            $document = $this->billingService->createDirectInvoice(
                $company,
                $customerData,
                $request->items,
                $request->payment_method_code
            );

            if ($document->isValidated()) {
                return redirect()->route('business.billing.documents.show', $document)
                    ->with('success', "Factura Electrónica {$document->full_number} emitida y validada exitosamente por la DIAN.");
            }

            return redirect()->route('business.billing.documents.show', $document)
                ->with('warning', "Factura registrada con observaciones: {$document->dian_response_message}");
        } catch (Exception $e) {
            return back()->with('error', 'Error al procesar la factura: ' . $e->getMessage());
        }
    }
}
