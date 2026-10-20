<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyFiscalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FiscalVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = CompanyFiscalProfile::with(['company.user', 'verifiedBy'])->latest();

        if ($status) {
            $query->where('fiscal_status', $status);
        }

        $profiles = $query->paginate(15)->withQueryString();

        $counts = [
            'in_review' => CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_IN_REVIEW)->count(),
            'verified'  => CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_VERIFIED)->count(),
            'enabled'   => CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_ENABLED)->count(),
            'rejected'  => CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_REJECTED)->count(),
            'all'       => CompanyFiscalProfile::count(),
        ];

        return view('admin.fiscal_verifications.index', compact('profiles', 'counts', 'status'));
    }

    public function show(CompanyFiscalProfile $profile)
    {
        $profile->load(['company.user', 'verifiedBy', 'company.billingConfiguration']);
        return view('admin.fiscal_verifications.show', compact('profile'));
    }

    public function verify(Request $request, CompanyFiscalProfile $profile)
    {
        $profile->update([
            'fiscal_status' => CompanyFiscalProfile::STATUS_PENDING_TECHNICAL_SETUP,
            'verified_at' => now(),
            'verified_by_user_id' => Auth::id(),
            'status_notes' => $request->input('notes', 'Verificación documental aprobada.'),
        ]);

        return redirect()->route('admin.fiscal_verifications.show', $profile)
            ->with('success', "Comercio {$profile->business_name} verificado legalmente. Se ha habilitado la configuración técnica.");
    }

    public function reject(Request $request, CompanyFiscalProfile $profile)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Debe ingresar el motivo del rechazo para orientar al comerciante.',
        ]);

        $profile->update([
            'fiscal_status' => CompanyFiscalProfile::STATUS_REJECTED,
            'status_notes'  => $request->reason,
            'verified_at'   => null,
            'verified_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.fiscal_verifications.show', $profile)
            ->with('warning', 'La solicitud de habilitación fiscal fue rechazada y se notificó el motivo.');
    }

    public function suspend(Request $request, CompanyFiscalProfile $profile)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $profile->update([
            'fiscal_status' => CompanyFiscalProfile::STATUS_SUSPENDED,
            'status_notes'  => $request->reason,
        ]);

        return redirect()->route('admin.fiscal_verifications.show', $profile)
            ->with('error', 'La habilitación fiscal del comercio ha sido suspendida.');
    }

    public function downloadDocument(CompanyFiscalProfile $profile, string $type)
    {
        $path = match ($type) {
            'rut' => $profile->rut_document_path,
            'camara' => $profile->camara_comercio_document_path,
            'rep_id' => $profile->legal_rep_id_document_path,
            default => null,
        };

        if (!$path) {
            return back()->with('error', 'El documento solicitado no existe.');
        }

        // Si el archivo está en public storage o local storage
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        return back()->with('error', 'El archivo no fue encontrado en el servidor.');
    }
}
