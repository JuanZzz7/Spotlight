<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public const CATEGORIES = [
        'Operación General',
        'Marketing Digital',
        'Insumos de Producción',
        'Distribución & Logística',
        'Servicios Públicos',
        'Arrendamiento',
        'Nómina y Honorarios',
        'Mantenimiento & Equipos',
        'Impuestos y Tasas',
        'Otros',
    ];

    public function index(Request $request)
    {
        $company  = Auth::user()->company;
        $tab      = $request->get('tab', 'egresos');
        $search   = $request->get('search');
        $category = $request->get('category');
        $month    = $request->get('month');

        $expenses = collect();
        $incomes  = collect();
        $transactions = collect();
        $categories = self::CATEGORIES;

        if ($company) {
            // Consulta de egresos
            $expQuery = $company->expenses()->latest();

            if ($search) {
                $expQuery->where(function ($q) use ($search) {
                    $q->where('concept', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if ($category && $category !== 'all') {
                $expQuery->where('category', $category);
            }

            if ($month) {
                $expQuery->whereYear('date', substr($month, 0, 4))
                         ->whereMonth('date', substr($month, 5, 2));
            }

            $expenses = $expQuery->get()->map(function ($e) {
                $e->type = 'expense';
                return $e;
            });

            // Consulta de ingresos (provenientes de OrderDetail de los productos de la empresa)
            $productIds = $company->products()->pluck('id');
            $incQuery = OrderDetail::whereIn('product_id', $productIds)->with(['order.user', 'product']);
            $rawIncomes = $incQuery->get();

            $incomes = $rawIncomes->map(function ($detail) {
                return (object)[
                    'id'          => 'inc_' . $detail->id,
                    'concept'     => 'Venta: ' . ($detail->product->name ?? 'Producto'),
                    'category'    => 'Ingreso Comercial',
                    'amount'      => $detail->subtotal ?: ($detail->price * $detail->quantity),
                    'date'        => $detail->order ? $detail->order->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'description' => 'Orden #' . $detail->order_id . ' - ' . $detail->quantity . ' und(s)',
                    'invoice_path' => null,
                    'invoice_filename' => null,
                    'invoice_url' => null,
                    'type'        => 'income',
                    'is_read'     => true,
                ];
            });

            if ($search) {
                $incomes = $incomes->filter(fn($i) => stripos($i->concept, $search) !== false);
            }

            if ($month) {
                $incomes = $incomes->filter(fn($i) => substr($i->date, 0, 7) === $month);
            }

            // Lógica de pestañas
            if ($tab === 'egresos') {
                $transactions = $expenses;
            } elseif ($tab === 'ingresos') {
                $transactions = $incomes->sortByDesc('date')->values();
            } else {
                $transactions = $expenses->concat($incomes)->sortByDesc('date')->values();
            }
        }

        return view('business.expenses.index', compact(
            'transactions', 'company', 'tab', 'search', 'category', 'month', 'expenses', 'incomes', 'categories'
        ));
    }

    public function create()
    {
        $company = Auth::user()->company;
        if (!$company) {
            return redirect()->route('business.company.edit')->with('warning', 'Primero configura tu empresa.');
        }

        $categories = self::CATEGORIES;
        return view('business.expenses.create', compact('categories', 'company'));
    }

    public function store(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) abort(403);

        $request->validate([
            'concept'     => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:1000',
            'invoice'     => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // 10MB
        ], [
            'invoice.mimes' => 'El soporte debe ser un archivo en formato PDF, JPG, PNG o WEBP.',
            'invoice.max'   => 'El archivo de soporte no debe exceder 10MB.',
        ]);

        $data = $request->only('concept', 'category', 'amount', 'date', 'description');
        $data['category'] = $data['category'] ?: 'Operación General';

        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $path = $file->store('expenses/invoices', 'public');
            $data['invoice_path'] = $path;
            $data['invoice_filename'] = $file->getClientOriginalName();
        }

        $company->expenses()->create($data);

        return redirect()->route('business.expenses.index')
            ->with('success', 'Egreso y soporte registrados exitosamente.');
    }

    public function edit(Expense $expense)
    {
        $company = Auth::user()->company;
        if (!$company || $expense->company_id !== $company->id) {
            abort(403, 'No tienes permiso para editar este egreso.');
        }

        $categories = self::CATEGORIES;
        return view('business.expenses.edit', compact('expense', 'company', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $company = Auth::user()->company;
        if (!$company || $expense->company_id !== $company->id) {
            abort(403, 'No tienes permiso para modificar este egreso.');
        }

        $request->validate([
            'concept'        => 'required|string|max:255',
            'category'       => 'nullable|string|max:100',
            'amount'         => 'required|numeric|min:0',
            'date'           => 'required|date',
            'description'    => 'nullable|string|max:1000',
            'invoice'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'remove_invoice' => 'nullable|boolean',
        ], [
            'invoice.mimes' => 'El soporte debe ser un archivo en formato PDF, JPG, PNG o WEBP.',
            'invoice.max'   => 'El archivo de soporte no debe exceder 10MB.',
        ]);

        $data = $request->only('concept', 'category', 'amount', 'date', 'description');
        $data['category'] = $data['category'] ?: ($expense->category ?: 'Operación General');

        // Reemplazo de factura / soporte
        if ($request->hasFile('invoice')) {
            if ($expense->invoice_path && Storage::disk('public')->exists($expense->invoice_path)) {
                Storage::disk('public')->delete($expense->invoice_path);
            }
            $file = $request->file('invoice');
            $path = $file->store('expenses/invoices', 'public');
            $data['invoice_path'] = $path;
            $data['invoice_filename'] = $file->getClientOriginalName();
        } elseif ($request->boolean('remove_invoice')) {
            if ($expense->invoice_path && Storage::disk('public')->exists($expense->invoice_path)) {
                Storage::disk('public')->delete($expense->invoice_path);
            }
            $data['invoice_path'] = null;
            $data['invoice_filename'] = null;
        }

        $expense->update($data);

        return redirect()->route('business.expenses.index')
            ->with('success', 'Egreso actualizado correctamente.');
    }

    public function destroy(Expense $expense)
    {
        $company = Auth::user()->company;
        if (!$company || $expense->company_id !== $company->id) {
            abort(403, 'No autorizado para eliminar este egreso.');
        }

        if ($expense->invoice_path && Storage::disk('public')->exists($expense->invoice_path)) {
            Storage::disk('public')->delete($expense->invoice_path);
        }

        $expense->delete();

        return redirect()->route('business.expenses.index')
            ->with('success', 'Egreso eliminado exitosamente.');
    }

    public function exportPdf(Request $request)
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }

        $search   = $request->get('search');
        $category = $request->get('category');
        $month    = $request->get('month');

        $query = $company->expenses()->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('concept', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($month) {
            $query->whereYear('date', substr($month, 0, 4))
                  ->whereMonth('date', substr($month, 5, 2));
        }

        $expenses = $query->get();
        $totalAmount = $expenses->sum('amount');
        $totalCount  = $expenses->count();
        $averageAmount = $totalCount > 0 ? $totalAmount / $totalCount : 0;
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) {
            return [
                'count'  => $group->count(),
                'amount' => $group->sum('amount'),
            ];
        });

        return view('business.expenses.expenses_pdf', compact(
            'company', 'expenses', 'totalAmount', 'totalCount', 'averageAmount', 'categoryBreakdown', 'search', 'category', 'month'
        ));
    }
}
