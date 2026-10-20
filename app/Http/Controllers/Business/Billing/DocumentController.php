<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ElectronicDocument;
use App\Services\Billing\ElectronicDocumentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
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

    public function index(Request $request)
    {
        $company = $this->getCompany();

        $query = $company->electronicDocuments()->with(['order', 'items', 'parentDocument'])->latest();

        // Filtro por tipo de documento
        if ($type = $request->input('document_type')) {
            $query->where('document_type', $type);
        }

        // Filtro por estado DIAN
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filtro por rango de fechas
        if ($from = $request->input('date_from')) {
            $query->whereDate('issue_date', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('issue_date', '<=', $to);
        }

        // Búsqueda por número o cliente
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_id_number', 'like', "%{$search}%")
                  ->orWhere('uuid_cufe', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(15)->withQueryString();

        return view('business.billing.documents.index', compact('company', 'documents'));
    }

    public function show(ElectronicDocument $document)
    {
        Gate::authorize('view', $document);

        $document->load(['items', 'company.fiscalProfile', 'company.billingConfiguration', 'creditNotes', 'debitNotes', 'logs' => function ($q) {
            $q->latest();
        }]);

        return view('business.billing.documents.show', compact('document'));
    }

    public function downloadXml(ElectronicDocument $document)
    {
        Gate::authorize('download', $document);

        if (!$document->xml_path || !Storage::disk('local')->exists($document->xml_path)) {
            // Si el XML aún no se guardó en disco pero se tiene el generador UBL
            $xmlContent = $this->billingService->generatePdfRepresentation($document);
            if (!$document->xml_path || !Storage::disk('local')->exists($document->xml_path)) {
                return back()->with('error', 'El archivo XML firmado aún no está disponible.');
            }
        }

        return Storage::disk('local')->download(
            $document->xml_path,
            "{$document->full_number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }

    public function downloadPdf(ElectronicDocument $document)
    {
        Gate::authorize('download', $document);

        // Renderizado directo de la representación gráfica oficial con QR y CUFE
        $html = $this->billingService->renderDocumentHtml($document);

        // Si se pide como vista imprimible o descarga
        if (request()->has('print')) {
            return response($html)->header('Content-Type', 'text/html');
        }

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "inline; filename=\"{$document->full_number}.html\"");
    }

    public function syncStatus(ElectronicDocument $document)
    {
        Gate::authorize('view', $document);

        try {
            $response = $this->billingService->syncDocumentStatus($document);
            if ($response->isSuccess) {
                return back()->with('success', 'Estado sincronizado con la DIAN: ' . $document->fresh()->dian_status);
            }
            return back()->with('error', 'Respuesta de la DIAN: ' . $response->message);
        } catch (Exception $e) {
            return back()->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }

    public function createCreditNote(Request $request, ElectronicDocument $document)
    {
        Gate::authorize('creditNote', $document);

        $request->validate([
            'correction_concept_code' => 'required|string|in:1,2,3,4,5',
            'correction_reason'       => 'required|string|max:500',
        ], [
            'correction_concept_code.required' => 'Debe seleccionar el concepto de corrección DIAN.',
            'correction_reason.required'       => 'Debe indicar el motivo detallado de la nota crédito.',
        ]);

        try {
            $creditNote = $this->billingService->createCreditNote(
                $document,
                $request->correction_concept_code,
                $request->correction_reason
            );

            if ($creditNote->isValidated()) {
                return redirect()->route('business.billing.documents.show', $creditNote)
                    ->with('success', "Nota Crédito Electrónica {$creditNote->full_number} emitida y validada exitosamente ante la DIAN.");
            }

            return redirect()->route('business.billing.documents.show', $creditNote)
                ->with('warning', "Nota Crédito emitida con observaciones: {$creditNote->dian_response_message}");
        } catch (Exception $e) {
            return back()->with('error', 'Error al emitir la Nota Crédito: ' . $e->getMessage());
        }
    }
}
