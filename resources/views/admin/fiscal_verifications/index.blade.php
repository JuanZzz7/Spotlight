@extends('layouts.dashboard')

@section('page-title', 'Verificaciones Fiscales DIAN')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="vs-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">Auditoría y Verificación Fiscal de Comercios</h4>
                <p class="text-muted small mb-0">
                    Revise las solicitudes de habilitación fiscal de los comercios formales de Ubaté para autorizar su emisión ante la DIAN.
                </p>
            </div>
            <div>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-building me-1"></i> Directorio de Empresas
                </a>
            </div>
        </div>
    </div>

    {{-- Pestañas de Filtrado por Estado --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.fiscal_verifications.index') }}" 
           class="btn btn-sm rounded-pill {{ empty($status) ? 'btn-primary' : 'btn-light border' }}">
            Todos ({{ $counts['all'] }})
        </a>
        <a href="{{ route('admin.fiscal_verifications.index', ['status' => 'in_review']) }}" 
           class="btn btn-sm rounded-pill {{ $status === 'in_review' ? 'btn-primary' : 'btn-light border' }}">
            En Revisión ({{ $counts['in_review'] }})
        </a>
        <a href="{{ route('admin.fiscal_verifications.index', ['status' => 'verified']) }}" 
           class="btn btn-sm rounded-pill {{ $status === 'verified' ? 'btn-primary' : 'btn-light border' }}">
            Verificados ({{ $counts['verified'] }})
        </a>
        <a href="{{ route('admin.fiscal_verifications.index', ['status' => 'enabled']) }}" 
           class="btn btn-sm rounded-pill {{ $status === 'enabled' ? 'btn-primary' : 'btn-light border' }}">
            Habilitados ({{ $counts['enabled'] }})
        </a>
        <a href="{{ route('admin.fiscal_verifications.index', ['status' => 'rejected']) }}" 
           class="btn btn-sm rounded-pill {{ $status === 'rejected' ? 'btn-primary' : 'btn-light border' }}">
            Rechazados ({{ $counts['rejected'] }})
        </a>
    </div>

    {{-- Tabla de Solicitudes --}}
    <div class="vs-card p-4">
        @if($profiles->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-check-circle fs-1 text-muted"></i>
                <h6 class="fw-bold text-dark mt-2 mb-1">No hay solicitudes en este estado</h6>
                <p class="text-muted small mb-0">Las empresas formales que completen su perfil fiscal aparecerán aquí.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Comercio</th>
                            <th>Razón Social & NIT</th>
                            <th>Régimen Fiscal</th>
                            <th>CIIU</th>
                            <th>Fecha Solicitud</th>
                            <th>Estado Habilitación</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $p->company->name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Prop: {{ $p->company->user?->name }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $p->business_name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">NIT: {{ $p->identification_number }}-{{ $p->dv }}</div>
                                </td>
                                <td class="small">
                                    {{ ucfirst(str_replace('_', ' ', $p->tax_regime)) }}
                                </td>
                                <td class="small">
                                    <span class="badge bg-light text-dark border">{{ $p->ciiu_code ?: 'N/A' }}</span>
                                </td>
                                <td class="small text-muted">
                                    {{ $p->submitted_at ? $p->submitted_at->format('d/m/Y H:i') : 'Sin fecha' }}
                                </td>
                                <td>
                                    <span class="badge {{ $p->status_badge_class }}" style="font-size: 0.7rem;">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.fiscal_verifications.show', $p) }}" class="btn btn-sm btn-spotlight rounded-pill px-3">
                                        Auditar &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
