<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\{Expense, Order, OrderDetail};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $income  = 0;
        $expenses = 0;
        $sales = collect();
        $weeklySales = [];
        $totalOrdersCount = 0;

        if ($company) {
            $productIds = $company->products->pluck('id');

            // Ingresos consolidados
            $income = OrderDetail::whereIn('product_id', $productIds)->sum('subtotal');
            if ($income <= 0) {
                $income = Order::where(function ($q) use ($company) {
                    $q->where('company_id', $company->id)
                      ->orWhereHas('details', function ($d) use ($company) {
                          $d->where('company_id', $company->id);
                      });
                })->where(function ($q) {
                    $q->where('payment_status', 'approved')->orWhere('status', 'paid');
                })->sum('net_company_amount');

                if ($income <= 0) {
                    $income = Order::where('company_id', $company->id)->sum('total');
                }
            }

            // Egresos registrados
            $expenses = Expense::where('company_id', $company->id)->sum('amount');

            // Ventas recientes
            $sales = OrderDetail::whereIn('product_id', $productIds)
                ->with(['product', 'order.user', 'order.shipment'])
                ->latest()
                ->take(10)
                ->get();

            $totalOrdersCount = Order::where(function ($q) use ($company) {
                $q->where('company_id', $company->id)
                  ->orWhereHas('details', function ($d) use ($company) {
                      $d->where('company_id', $company->id);
                  });
            })->count();

            // Actividad semanal de ventas (últimos 7 días)
            $days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
            $startOfWeek = Carbon::now()->startOfWeek();
            $maxAmount = 1;
            $weekData = [];

            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dayAmount = OrderDetail::whereIn('product_id', $productIds)
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('subtotal');
                if ($dayAmount > $maxAmount) {
                    $maxAmount = $dayAmount;
                }
                $weekData[] = [
                    'day' => $days[$i],
                    'date' => $date->format('d/m'),
                    'amount' => (float)$dayAmount,
                    'is_today' => $date->isToday(),
                ];
            }

            foreach ($weekData as &$item) {
                $item['percentage'] = $maxAmount > 0 ? max(15, min(100, (int)round(($item['amount'] / $maxAmount) * 100))) : 20;
            }
            $weeklySales = $weekData;
        }

        $net = $income - $expenses;
        return view('business.dashboard', compact('company', 'income', 'expenses', 'net', 'sales', 'weeklySales', 'totalOrdersCount'));
    }
}
