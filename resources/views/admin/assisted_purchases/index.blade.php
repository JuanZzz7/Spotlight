@extends('layouts.dashboard')
@section('title', 'Supervisión de Compras Internacionales - Admin Spotlight')
@section('page-title', 'Compras Internacionales Asistidas (CO ⇄ MX)')

@section('dashboard-content')
{{-- Header Area --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">Supervisión de Compras Internacionales</h3>
        <p class="text-muted mb-0" style="font-size: 0.88rem;">
            Monitoreo y auditoría de solicitudes asistidas entre emprendimientos y compradores de Colombia 🇨🇴 y México 🇲🇽.
        </p>
    </div>
</div>

{{-- Estadísticas Generales --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: var(--sl-primary-soft, #F6E7EB); color: var(--sl-primary, #7F1D3F); width: 44px; height: 44px;">
                <i class="bi bi-globe2 fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">TOTAL SOLICITUDES</div>
                <div class="fw-bold text-dark lh-1 fs-4 mt-1">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(252, 209, 22, 0.2); color: #b45309; width: 44px; height: 44px;">
                <span class="fs-4">🇨🇴</span>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">ORIGEN COLOMBIA</div>
                <div class="fw-bold text-dark lh-1 fs-4 mt-1">{{ $stats['from_colombia'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(0, 104, 71, 0.15); color: #006847; width: 44px; height: 44px;">
                <span class="fs-4">🇲🇽</span>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">ORIGEN MÉXICO</div>
                <div class="fw-bold text-dark lh-1 fs-4 mt-1">{{ $stats['from_mexico'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-4 border shadow-xs">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5; width: 44px; height: 44px;">
                <i class="bi bi-arrow-repeat fs-4"></i>
            </div>
            <div>
                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">EN FLUJO ACTIVO</div>
                <div class="fw-bold text-primary lh-1 fs-4 mt-1">{{ $stats['active_in_flow'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filtros de Búsqueda y Rutas --}}
<div class="bg-white p-3 rounded-4 border shadow-xs mb-4">
    <form action="{{ route('admin.assisted_purchases.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label text-xs fw-bold text-uppercase text-muted">Buscar Solicitud o Emprendimiento</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Código, destinatario, tienda..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label text-xs fw-bold text-uppercase text-muted">Origen</label>
            <select name="origin" class="form-select">
                <option value="">Todos los orígenes</option>
                <option value="CO" {{ request('origin') === 'CO' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                <option value="MX" {{ request('origin') === 'MX' ? 'selected' : '' }}>🇲🇽 México</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label text-xs fw-bold text-uppercase text-muted">Destino</label>
            <select name="destination" class="form-select">
                <option value="">Todos los destinos</option>
                <option value="CO" {{ request('destination') === 'CO' ? 'selected' : '' }}>🇨🇴 Colombia</option>
                <option value="MX" {{ request('destination') === 'MX' ? 'selected' : '' }}>🇲🇽 México</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label text-xs fw-bold text-uppercase text-muted">Estado</label>
            <select name="status" class="form-select">
                <option value="">Todos los estados</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 d-flex">
            <button type="submit" class="btn btn-dark w-100 fw-bold" title="Aplicar Filtros">
                <i class="bi bi-funnel-fill"></i>
            </button>
        </div>
    </form>
</div>

{{-- Tabla de Solicitudes Internacionales --}}
<div class="bg-white rounded-4 border shadow-xs overflow-hidden">
    @if($requests->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; color: #64748b;">
                    <tr>
                        <th class="ps-4 py-3">Código</th>
                        <th class="py-3">Ruta</th>
                        <th class="py-3">Producto / Emprendimiento</th>
                        <th class="py-3">Comprador</th>
                        <th class="py-3">Total Acordado</th>
                        <th class="py-3 text-center">Estado</th>
                        <th class="pe-4 py-3 text-end">Detalle</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @foreach($requests as $req)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="fw-bold font-monospace text-dark">{{ $req->request_code }}</span>
                                <div class="text-muted text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-1 fw-bold">
                                    <span>{{ $req->origin_country === 'MX' ? '🇲🇽 MX' : '🇨🇴 CO' }}</span>
                                    <i class="bi bi-arrow-right text-muted"></i>
                                    <span>{{ $req->destination_country === 'MX' ? '🇲🇽 MX' : '🇨🇴 CO' }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $req->product_name }}</div>
                                <div class="text-muted text-xs">Vendido por: <strong>{{ $req->company->name ?? 'N/A' }}</strong></div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-dark">{{ $req->recipient_name }}</div>
                                <div class="text-muted text-xs">{{ $req->destination_city }}, {{ $req->destination_state }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-black text-dark">{{ $req->formatted_total_agreed }}</div>
                                <div class="text-muted text-xs">Base: {{ $req->formatted_original_price }}</div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge {{ $req->status_badge }} rounded-pill px-3 py-1">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <a href="{{ route('admin.assisted_purchases.show', $req) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold shadow-xs">
                                    <i class="bi bi-eye"></i> Auditar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $requests->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5 p-4">
            <i class="bi bi-globe fs-1 text-muted d-block mb-2"></i>
            <h5 class="fw-bold text-dark">No se encontraron solicitudes internacionales</h5>
            <p class="text-muted mb-0">No hay registros que coincidan con los criterios de búsqueda seleccionados.</p>
        </div>
    @endif
</div>
@endsection
