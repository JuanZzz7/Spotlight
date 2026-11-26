<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Company, CompanyFiscalProfile, AssistedPurchase, Product, Order, Shipment, Review};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Panel principal de administración de la plataforma con centro analítico interactivo.
     */
    public function index()
    {
        $totalUsers      = User::where('role', 'user')->count();
        $totalBusinesses = User::where('role', 'business')->count();
        $totalAdmins     = User::where('role', 'admin')->count();
        $verifiedUsers   = User::whereNotNull('email_verified_at')->count();
        $allUsersCount   = max(1, $totalUsers + $totalBusinesses + $totalAdmins);

        $totalCompanies    = Company::count();
        $activeCompanies   = Company::where('status', 'active')->count();
        $pendingCompanies  = Company::where('status', 'pending')->count();
        $inactiveCompanies = max(0, $totalCompanies - $activeCompanies - $pendingCompanies);

        $coCompanies = Company::where(function ($q) {
            $q->where('country', 'CO')->orWhereNull('country');
        })->count();
        $mxCompanies = Company::where('country', 'MX')->count();

        $formalCompanies   = Company::where(fn($q) => $q->where('country', 'CO')->orWhereNull('country'))->where('tipo_negocio', 'formal')->count();
        $informalCompanies = max(0, $coCompanies - $formalCompanies);
        $mappedCompanies   = Company::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $companiesWithProd = Company::whereHas('products', fn($q) => $q->where('active', true))->count();

        $dianEnabled  = CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_ENABLED)->count();
        $dianInReview = CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_IN_REVIEW)->count();
        $dianVerified = CompanyFiscalProfile::where('fiscal_status', CompanyFiscalProfile::STATUS_VERIFIED)->count();

        $activeProductsCount = Product::where('active', true)->count();
        $totalOrdersCount    = Order::count();
        $totalShipmentsCount = Shipment::count();
        $totalReviewsCount   = Review::count();

        $activeAssisted = AssistedPurchase::whereNotIn('status', [
            AssistedPurchase::STATUS_CANCELLED,
            AssistedPurchase::STATUS_REJECTED,
            AssistedPurchase::STATUS_DELIVERED
        ])->count();

        $stats = [
            'users'                 => $totalUsers,
            'businesses'            => $totalBusinesses,
            'companies'             => $totalCompanies,
            'active_companies'      => $activeCompanies,
            'pending_companies'     => $pendingCompanies,
            'pending_verifications' => $dianInReview,
            'dian_enabled'          => $dianEnabled,
            'assisted_purchases'    => $activeAssisted,
            'active_products'       => $activeProductsCount,
            'total_orders'          => $totalOrdersCount,
            'total_shipments'       => $totalShipmentsCount,
            'total_reviews'         => $totalReviewsCount,
            'verified_pct'          => round(($verifiedUsers / $allUsersCount) * 100),
            'active_companies_pct'  => $totalCompanies > 0 ? round(($activeCompanies / $totalCompanies) * 100) : 0,
        ];

        // 1. Series temporales para el gráfico principal ("Pulso del Ecosistema")
        // Rango A: Últimos 7 días
        $labels7d = [];
        $users7d = [];
        $companies7d = [];
        $activity7d = [];
        $dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->toDateString();
            $labels7d[] = $dayNames[$date->dayOfWeek] . ' ' . $date->format('d/m');
            $users7d[] = User::whereDate('created_at', $dateStr)->count();
            $companies7d[] = Company::whereDate('created_at', $dateStr)->count();
            $activity7d[] = Order::whereDate('created_at', $dateStr)->count()
                          + Product::whereDate('created_at', $dateStr)->count()
                          + Review::whereDate('created_at', $dateStr)->count();
        }

        // Rango B: Últimos 30 días (10 bloques de 3 días)
        $labels30d = [];
        $users30d = [];
        $companies30d = [];
        $activity30d = [];

        for ($i = 9; $i >= 0; $i--) {
            $end = Carbon::today()->subDays($i * 3)->endOfDay();
            $start = Carbon::today()->subDays(($i * 3) + 2)->startOfDay();
            $labels30d[] = $start->format('d/m') . '-' . $end->format('d/m');
            $users30d[] = User::whereBetween('created_at', [$start, $end])->count();
            $companies30d[] = Company::whereBetween('created_at', [$start, $end])->count();
            $activity30d[] = Order::whereBetween('created_at', [$start, $end])->count()
                           + Product::whereBetween('created_at', [$start, $end])->count()
                           + Review::whereBetween('created_at', [$start, $end])->count();
        }

        // Rango C: Últimos 6 meses
        $labels6m = [];
        $users6m = [];
        $companies6m = [];
        $activity6m = [];
        $monthNames = [1=>'Ene', 2=>'Feb', 3=>'Mar', 4=>'Abr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Ago', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dic'];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->startOfMonth()->subMonths($i);
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();
            $labels6m[] = $monthNames[$monthDate->month] . ' ' . $monthDate->format('y');
            $users6m[] = User::whereBetween('created_at', [$mStart, $mEnd])->count();
            $companies6m[] = Company::whereBetween('created_at', [$mStart, $mEnd])->count();
            $activity6m[] = Order::whereBetween('created_at', [$mStart, $mEnd])->count()
                          + Product::whereBetween('created_at', [$mStart, $mEnd])->count()
                          + Review::whereBetween('created_at', [$mStart, $mEnd])->count();
        }

        // 2. Distribución de empresas por categoría
        $categoryRows = Company::selectRaw('COALESCE(NULLIF(TRIM(category), ""), "Comercio General") as cat_label, COUNT(*) as total')
            ->groupBy('cat_label')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        if ($categoryRows->isEmpty()) {
            $categoryLabels = ['Sin empresas aún'];
            $categoryValues = [0];
        } else {
            $categoryLabels = $categoryRows->pluck('cat_label')->values()->all();
            $categoryValues = $categoryRows->pluck('total')->map(fn($v) => (int) $v)->values()->all();
        }

        // 3. Métricas de Salud del Ecosistema (0 a 100%) para el Radar Interactivo
        $companyDenom = max(1, $totalCompanies);
        $radarScores = [
            min(100, (int) round(($verifiedUsers / $allUsersCount) * 100)),
            min(100, (int) round(($activeCompanies / $companyDenom) * 100)),
            min(100, (int) round(($companiesWithProd / $companyDenom) * 100)),
            min(100, (int) round(($formalCompanies / $companyDenom) * 100)),
            min(100, (int) round(($mappedCompanies / $companyDenom) * 100)),
            min(100, $activeProductsCount > 0 ? min(100, (int) round((($totalOrdersCount + $totalReviewsCount + $activeProductsCount) / max(1, $activeProductsCount)) * 45)) : 0),
        ];

        $analytics = [
            'timeline' => [
                '7d'  => ['labels' => $labels7d, 'users' => $users7d, 'companies' => $companies7d, 'activity' => $activity7d],
                '30d' => ['labels' => $labels30d, 'users' => $users30d, 'companies' => $companies30d, 'activity' => $activity30d],
                '6m'  => ['labels' => $labels6m, 'users' => $users6m, 'companies' => $companies6m, 'activity' => $activity6m],
            ],
            'distributions' => [
                'categories' => [
                    'labels' => $categoryLabels,
                    'values' => $categoryValues,
                ],
                'formality' => [
                    'labels' => ['Formal (DIAN Activa)', 'Formal (En proceso)', 'Emprendimiento Local'],
                    'values' => [
                        $dianEnabled,
                        max(0, $formalCompanies - $dianEnabled),
                        $informalCompanies,
                    ],
                ],
                'status_market' => [
                    'labels' => ['Colombia (Activas)', 'México (Activas)', 'Pendientes de Aprobación', 'Inactivas'],
                    'values' => [
                        Company::where('status', 'active')->where(fn($q) => $q->where('country', 'CO')->orWhereNull('country'))->count(),
                        Company::where('status', 'active')->where('country', 'MX')->count(),
                        $pendingCompanies,
                        $inactiveCompanies,
                    ],
                ],
            ],
            'roles_breakdown' => [
                'buyers_verified'   => User::where('role', 'user')->whereNotNull('email_verified_at')->count(),
                'buyers_unverified' => User::where('role', 'user')->whereNull('email_verified_at')->count(),
                'merchants_formal'  => $formalCompanies,
                'merchants_local'   => $informalCompanies,
                'admins'            => $totalAdmins,
            ],
            'radar' => [
                'labels' => [
                    'Verificación Email',
                    'Empresas Activas',
                    'Tiendas con Catálogo',
                    'Formalización DIAN',
                    'Cobertura en Mapa',
                    'Tracción Comercial',
                ],
                'scores' => $radarScores,
            ],
            'markets' => [
                'co' => $coCompanies,
                'mx' => $mxCompanies,
            ],
        ];

        // Datos recientes de la base de datos reflejados en vivo para supervisión del Administrador
        $recentUsers = User::latest()->take(6)->get();
        $recentCompanies = Company::with('user')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'analytics', 'recentUsers', 'recentCompanies'));
    }
}
