<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Inventario_{{ \Illuminate\Support\Str::slug($company->name) }}_{{ date('Ymd') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 30px;
            font-size: 13px;
        }
        .pdf-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
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
            gap: 12px;
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 8px;
        }
        .company-info h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .company-info p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }
        .doc-meta {
            text-align: right;
        }
        .doc-meta h1 {
            font-size: 16px;
            font-weight: 800;
            color: #7F1D3F;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .doc-meta p {
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }
        .summary-kpis {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .kpi-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #7F1D3F;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            color: #334155;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #94a3b8;
        }
        .no-print-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }
        .btn-print {
            background: #7F1D3F;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(127,29,63,0.3);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-print:hover {
            background: #631530;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .pdf-container {
                box-shadow: none;
                border: none;
                padding: 0;
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
    <button onclick="window.print()" class="btn-print">
        <i class="bi bi-printer"></i> Imprimir / Guardar como PDF
    </button>
    <button onclick="window.close()" class="btn-print" style="background: #475569;">
        <i class="bi bi-x-lg"></i> Cerrar
    </button>
</div>

<div class="pdf-container">
    <div class="header">
        <div class="logo-title">
            <img src="{{ asset('images/logo-symbol-burgundy.png') }}" class="brand-logo" alt="Spotlight Logo">
            <div class="company-info">
                <h2>{{ $company->name }}</h2>
                <p>
                    @if($company->nit) NIT: {{ $company->nit }} • @endif
                    {{ $company->category ?: 'Comercio Local' }}<br>
                    {{ $company->address ?: 'Ubicación registrada' }} • Tel: {{ $company->phone ?: 'No disponible' }}
                </p>
            </div>
        </div>
        <div class="doc-meta">
            <h1>Reporte de Inventario</h1>
            <p>
                <strong>Fecha:</strong> {{ now()->setTimezone('America/Bogota')->format('d/m/Y h:i A') }}<br>
                <strong>Filtro:</strong> {{ ucfirst($status === 'all' ? 'Todos los productos' : ($status === 'active' ? 'Solo Activos' : 'Solo Agotados')) }}<br>
                <strong>Generado por:</strong> {{ auth()->user()->name }}
            </p>
        </div>
    </div>

    <div class="summary-kpis">
        <div class="kpi-card">
            <div class="kpi-label">Total Referencias</div>
            <div class="kpi-value">{{ count($products) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Unidades en Stock</div>
            <div class="kpi-value">{{ number_format($totalUnits, 0, ',', '.') }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Valoración Total (COP)</div>
            <div class="kpi-value" style="color: #7F1D3F;">${{ number_format($totalValue, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Producto</th>
                <th style="width: 15%;">SKU</th>
                <th style="width: 15%; text-align: right;">Precio Unit.</th>
                <th style="width: 12%; text-align: center;">Stock</th>
                <th style="width: 18%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $prod)
                @php $prodTotal = $prod->price * $prod->stock; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $prod->name }}</strong>
                        @if(!$prod->active)
                            <span class="badge badge-secondary ms-1">Inactivo</span>
                        @endif
                    </td>
                    <td>{{ $prod->sku ?: 'S/N' }}</td>
                    <td style="text-align: right;">${{ number_format($prod->price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        @if($prod->stock <= 0)
                            <span class="badge badge-danger">0 (Agotado)</span>
                        @else
                            <span class="badge badge-success">{{ $prod->stock }}</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: 600;">${{ number_format($prodTotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 25px; color: #64748b;">
                        No hay productos registrados en el inventario para el filtro seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: 800;">
                <td colspan="4" style="text-align: right; padding: 12px; font-size: 13px;">TOTAL INVENTARIO:</td>
                <td style="text-align: center; padding: 12px; font-size: 13px;">{{ number_format($totalUnits, 0, ',', '.') }}</td>
                <td style="text-align: right; padding: 12px; font-size: 13px; color: #7F1D3F;">${{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <span>SPOTLIGHT Marketplace • Reporte oficial de inventario de comercio</span>
        <span>Página 1 de 1</span>
    </div>
</div>

<script>
    // Iniciar automáticamente el diálogo de impresión al cargar
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>

</body>
</html>
