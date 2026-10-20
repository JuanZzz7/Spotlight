<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Egresos_{{ \Illuminate\Support\Str::slug($company->name) }}_{{ date('Ymd_His') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 30px;
            font-size: 13px;
            line-height: 1.5;
        }
        .pdf-container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .no-print-bar {
            max-width: 960px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: #7F1D3F;
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(127,29,63,0.3);
            text-decoration: none;
        }
        .btn-back {
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #7F1D3F;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 4px;
        }
        .company-info h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .company-info p {
            font-size: 12px;
            color: #64748b;
        }
        .report-meta {
            text-align: right;
        }
        .report-badge {
            background: #F6E7EB;
            color: #7F1D3F;
            font-weight: 800;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .report-meta h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #7F1D3F;
            margin-bottom: 4px;
        }
        .report-meta p {
            font-size: 11px;
            color: #64748b;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
        }
        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .stat-value.primary { color: #7F1D3F; }
        .stat-value.danger { color: #dc2626; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
        }
        .table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 2px solid #cbd5e1;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .table tr:nth-child(even) {
            background: #fafafa;
        }
        .badge-cat {
            background: #F6E7EB;
            color: #7F1D3F;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .badge-soporte {
            background: #dbeafe;
            color: #1e40af;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .tfoot-total {
            background: #f8fafc;
            font-weight: 800;
            border-top: 2px solid #0f172a;
        }
        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #94a3b8;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .pdf-container {
                box-shadow: none;
                border: none;
                padding: 10px 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <a href="{{ route('business.expenses.index') }}" class="btn-back">
        &larr; Volver a Egresos
    </a>
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer-fill"></i> Imprimir / Guardar como PDF
        </button>
    </div>
</div>

<div class="pdf-container">
    {{-- Header --}}
    <div class="header">
        <div class="logo-title">
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="logo-img">
            @else
                <img src="{{ asset('images/logo-symbol-burgundy.png') }}" alt="Spotlight" class="logo-img">
            @endif
            <div class="company-info">
                <h2>{{ $company->razon_social ?: $company->name }}</h2>
                <p>NIT: {{ $company->nit ?: 'N/A' }} | Tel: {{ $company->phone ?: 'N/A' }}</p>
                <p>{{ $company->address ?: 'Ubaté, Cundinamarca' }} | Email: {{ $company->email ?: 'comercio@spotlight.local' }}</p>
            </div>
        </div>
        <div class="report-meta">
            <span class="report-badge">Control Financiero Spotlight</span>
            <h1>Reporte de Egresos</h1>
            <p><strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}</p>
            @if($month)
                <p><strong>Periodo:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</p>
            @else
                <p><strong>Periodo:</strong> Histórico Completo</p>
            @endif
            @if($category && $category !== 'all')
                <p><strong>Filtro Categoría:</strong> {{ $category }}</p>
            @endif
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Egresos</div>
            <div class="stat-value danger">${{ number_format($totalAmount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Registros</div>
            <div class="stat-value">{{ $totalCount }} transacciones</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Promedio por Gasto</div>
            <div class="stat-value">${{ number_format($averageAmount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Con Soporte Adjunto</div>
            <div class="stat-value primary">{{ $expenses->whereNotNull('invoice_path')->count() }} facturas</div>
        </div>
    </div>

    {{-- Tabla de Egresos --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 35px;">#</th>
                <th style="width: 85px;">Fecha</th>
                <th>Concepto del Gasto</th>
                <th style="width: 140px;">Categoría</th>
                <th>Descripción / Observación</th>
                <th style="width: 130px;">Soporte Factura</th>
                <th style="text-align: right; width: 110px;">Monto (COP)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $expense)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                    <td><strong>{{ $expense->concept }}</strong></td>
                    <td><span class="badge-cat">{{ $expense->category ?: 'Operación General' }}</span></td>
                    <td>{{ $expense->description ?: 'Sin observaciones' }}</td>
                    <td>
                        @if($expense->invoice_path)
                            <span class="badge-soporte">
                                <i class="bi bi-paperclip"></i> {{ \Illuminate\Support\Str::limit($expense->invoice_filename ?: 'Adjunto', 16) }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-size: 11px;">Sin soporte</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #b91c1c;">
                        ${{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">
                        No hay egresos registrados en el periodo y filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="tfoot-total">
                <td colspan="6" style="text-align: right; padding: 12px; font-size: 13px;">TOTAL EGRESOS REGISTRADOS:</td>
                <td style="text-align: right; padding: 12px; font-size: 14px; color: #b91c1c;">
                    ${{ number_format($totalAmount, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <span>SPOTLIGHT Marketplace • Documento de control interno y auditoría contable</span>
        <span>Generado por: {{ auth()->user()->name }}</span>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 600);
    });
</script>

</body>
</html>
