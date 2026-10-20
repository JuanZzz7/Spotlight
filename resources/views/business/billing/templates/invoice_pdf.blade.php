<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->type_label }} {{ $document->full_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            background-color: #ffffff;
        }
        .header-table, .info-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .border-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .totals-table td {
            padding: 4px 8px;
        }
        .cufe-box {
            background-color: #f8fafc;
            border: 1px dashed #94a3b8;
            padding: 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 8px;
            word-break: break-all;
            margin-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            background-color: #0284c7;
            color: white;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    {{-- Barra superior de acciones para pantalla --}}
    <div class="no-print" style="margin-bottom: 20px; padding: 12px; background-color: #f1f5f9; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong style="color: #7F1D3F;">SPOTLIGHT</strong> — Representación Gráfica Oficial DIAN (Anexo Técnico 1.9)
        </div>
        <div>
            <button onclick="window.print();" style="padding: 6px 16px; background-color: #7F1D3F; color: white; border: none; border-radius: 20px; cursor: pointer; font-weight: bold;">
                Imprimir / Guardar como PDF
            </button>
            <button onclick="window.close();" style="padding: 6px 14px; background-color: #64748b; color: white; border: none; border-radius: 20px; cursor: pointer; margin-left: 8px;">
                Cerrar
            </button>
        </div>
    </div>

    {{-- Encabezado Principal --}}
    <table class="header-table" style="margin-bottom: 16px;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <h2 style="margin: 0 0 4px 0; color: #0f172a; font-size: 16px;">{{ $document->issuer_business_name }}</h2>
                <div class="fw-bold" style="font-size: 11px; margin-bottom: 4px;">NIT: {{ $document->issuer_nit }}-{{ $document->issuer_dv }}</div>
                <div class="text-muted" style="line-height: 1.4;">
                    Régimen: {{ ucfirst(str_replace('_', ' ', $document->issuer_tax_regime)) }}<br>
                    Dirección: {{ $document->issuer_address }}<br>
                    Ubicación: Ubaté, Cundinamarca, Colombia (Código DANE: 25843)<br>
                    Correo: {{ $document->issuer_email }} | Teléfono: {{ $document->issuer_phone }}
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div class="border-box text-center" style="background-color: #FFFCF7; border-color: #EEDDDC;">
                    <div style="font-size: 10px; font-weight: bold; color: #7F1D3F; text-transform: uppercase;">
                        {{ $document->type_label }}
                    </div>
                    <h3 style="margin: 4px 0; color: #1e293b; font-size: 18px;">{{ $document->full_number }}</h3>
                    <div style="font-size: 9px; color: #64748b; line-height: 1.3;">
                        Resolución DIAN N° {{ $config ? $config->resolution_number : '18760000001' }}<br>
                        Vigencia: {{ $config && $config->resolution_date_from ? $config->resolution_date_from->format('d/m/Y') : '01/01/2026' }} al {{ $config && $config->resolution_date_to ? $config->resolution_date_to->format('d/m/Y') : '31/12/2026' }}<br>
                        Prefijo: {{ $document->prefix }} | Rango: {{ $config ? $config->range_from : 1 }} a {{ $config ? $config->range_to : 5000 }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Datos de la Factura y del Adquirente --}}
    <table class="info-table" style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div class="border-box" style="margin-bottom: 0;">
                    <div class="fw-bold" style="font-size: 10px; color: #475569; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 6px;">
                        DATOS DEL ADQUIRENTE (CLIENTE)
                    </div>
                    <div><strong>Nombre / Razón Social:</strong> {{ $document->customer_name }}</div>
                    <div><strong>Identificación:</strong> {{ $document->customer_id_number }} (Tipo: {{ $document->customer_id_type }})</div>
                    <div><strong>Dirección:</strong> {{ $document->customer_address ?: 'Ubaté, Cundinamarca' }}</div>
                    <div><strong>Municipio:</strong> Ubaté ({{ $document->customer_municipality_code }})</div>
                    <div><strong>Correo Electrónico:</strong> {{ $document->customer_email ?: 'No registrado' }}</div>
                    <div><strong>Teléfono:</strong> {{ $document->customer_phone ?: 'No registrado' }}</div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div class="border-box" style="margin-bottom: 0;">
                    <div class="fw-bold" style="font-size: 10px; color: #475569; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 6px;">
                        INFORMACIÓN DE EXPEDICIÓN
                    </div>
                    <div><strong>Fecha de Emisión:</strong> {{ $document->issue_date->format('d/m/Y') }}</div>
                    <div><strong>Hora de Emisión:</strong> {{ $document->issue_time }}</div>
                    <div><strong>Fecha de Vencimiento:</strong> {{ $document->due_date ? $document->due_date->format('d/m/Y') : $document->issue_date->format('d/m/Y') }}</div>
                    <div><strong>Forma de Pago:</strong> {{ $document->payment_means_code === '2' ? 'Crédito' : 'Contado' }}</div>
                    <div><strong>Medio de Pago:</strong> Código DIAN {{ $document->payment_method_code }}</div>
                    <div><strong>Moneda:</strong> COP (Pesos Colombianos)</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tabla de Ítems --}}
    <table class="items-table" style="margin-bottom: 14px;">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 70px;">Código</th>
                <th>Descripción</th>
                <th style="width: 35px;" class="text-center">Ud</th>
                <th style="width: 45px;" class="text-center">Cant</th>
                <th style="width: 75px;" class="text-end">Precio Unit.</th>
                <th style="width: 45px;" class="text-center">IVA %</th>
                <th style="width: 70px;" class="text-end">Valor IVA</th>
                <th style="width: 85px;" class="text-end">Total Ítem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document->items as $it)
                <tr>
                    <td class="text-center">{{ $it->line_number }}</td>
                    <td>{{ $it->product_code }}</td>
                    <td>{{ $it->description }}</td>
                    <td class="text-center">{{ $it->unit_measure }}</td>
                    <td class="text-center">{{ $it->quantity }}</td>
                    <td class="text-end">${{ number_format($it->price_amount, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $it->tax_rate }}%</td>
                    <td class="text-end">${{ number_format($it->tax_amount, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">${{ number_format($it->total_item_amount, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Cuadro de Totales y QR --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="width: 45%; vertical-align: top;">
                @if($document->qr_content)
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($document->qr_content) }}" alt="QR DIAN" style="width: 90px; height: 90px; border: 1px solid #cbd5e1;">
                        <div style="font-size: 8px; color: #475569; line-height: 1.3;">
                            <strong>VERIFICACIÓN DIAN</strong><br>
                            Escanee el código QR para consultar el documento en el Catálogo de Facturación Electrónica de la DIAN.
                        </div>
                    </div>
                @endif
            </td>
            <td style="width: 55%; vertical-align: top;">
                <table class="totals-table border-box" style="width: 100%; font-size: 11px;">
                    <tr>
                        <td class="text-end fw-bold" style="width: 65%;">Subtotal (Base Gravable):</td>
                        <td class="text-end fw-bold">${{ number_format($document->tax_exclusive_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-end text-muted">Descuentos Aplicados:</td>
                        <td class="text-end text-muted">-${{ number_format($document->allowance_total_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-end fw-bold" style="color: #0284c7;">IVA Total Liquidado:</td>
                        <td class="text-end fw-bold" style="color: #0284c7;">${{ number_format($document->tax_total_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #cbd5e1; font-size: 13px;">
                        <td class="text-end fw-bold" style="color: #0f172a; padding-top: 6px;">TOTAL A PAGAR (COP):</td>
                        <td class="text-end fw-bold" style="color: #0f172a; padding-top: 6px;">${{ number_format($document->payable_amount, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Bloque Obligatorio DIAN: CUFE / CUDE --}}
    <div class="cufe-box">
        <strong>{{ $document->isCreditNote() ? 'CUDE:' : 'CUFE:' }}</strong> {{ $document->uuid_cufe ?: 'PENDING_VALIDATION' }}
    </div>

    {{-- Leyenda Legal --}}
    <div style="font-size: 8px; color: #64748b; line-height: 1.4; margin-top: 10px; text-align: justify;">
        Factura Electrónica de Venta con Validación Previa expedida conforme al Decreto 1625 de 2016 y la Resolución DIAN 000165 de 2023. Esta factura presta mérito ejecutivo en los términos del artículo 774 del Código de Comercio. Generado mediante la plataforma Spotlight (Ubaté, Cundinamarca).
    </div>
</body>
</html>
