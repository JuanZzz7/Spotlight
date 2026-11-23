<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Company, CompanyFiscalProfile, AssistedPurchase};

class DashboardController extends Controller
{
    /**
     * Panel principal de administración de la plataforma.
     * Enfocado exclusivamente en gobernanza, usuarios, verificación legal/fiscal de empresas
     * y supervisión internacional, sin acceso a catálogos ni finanzas privadas de comercios.
     */
    public function index()
    {
        $stats = [
            'users'                 => User::where('role', 'user')->count(),
            'businesses'            => User::where('role', 'business')->count(),
            'companies'             => Company::count(),
            'pending_companies'     => Company::where('status', 'pending')->count(),
            'pending_verifications' => CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_IN_REVIEW)->count(),
            'assisted_purchases'    => AssistedPurchase::whereNotIn('status', [
                AssistedPurchase::STATUS_CANCELLED,
                AssistedPurchase::STATUS_REJECTED,
                AssistedPurchase::STATUS_DELIVERED
            ])->count(),
        ];

        // Datos recientes de la base de datos reflejados en vivo para supervisión del Administrador
        $recentUsers = User::latest()->take(5)->get();
        $recentCompanies = Company::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCompanies'));
    }
}
