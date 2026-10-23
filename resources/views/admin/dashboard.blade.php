@extends('layouts.dashboard')
@section('title','Admin - Spotlight')
@section('page-title','Panel Administrador')

@section('sidebar-menu')
<a href="{{ route('admin.dashboard') }}" class="vs-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid fs-5"></i> Panel principal
</a>
<a href="{{ route('admin.users.index') }}" class="vs-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
    <i class="bi bi-people fs-5"></i> Gestión de usuarios
</a>
<a href="{{ route('admin.companies.index') }}" class="vs-nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
    <i class="bi bi-building fs-5"></i> Gestión de empresas
</a>
<a href="{{ route('admin.fiscal_verifications.index') }}" class="vs-nav-link {{ request()->routeIs('admin.fiscal_verifications*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-check fs-5"></i> Verificación DIAN
</a>
<a href="{{ route('admin.assisted_purchases.index') }}" class="vs-nav-link {{ request()->routeIs('admin.assisted_purchases*') ? 'active' : '' }}">
    <i class="bi bi-globe-americas fs-5"></i> Solicitudes Int.
</a>
<a href="{{ route('chat.index') }}" class="vs-nav-link {{ request()->is('chat*') ? 'active' : '' }}">
    <i class="bi bi-chat-dots fs-5"></i> Mensajes
</a>
@endsection

@section('dashboard-content')
<div class="row g-4 mb-4">
    @php
    $cards = [
        ['icon'=>'bi-people','label'=>'Usuarios Registrados','value'=>$stats['users'],'color'=>'#7F1D3F'],
        ['icon'=>'bi-briefcase','label'=>'Emprendedores / Comercios','value'=>$stats['businesses'],'color'=>'#D98C9A'],
        ['icon'=>'bi-building','label'=>'Empresas en Plataforma','value'=>$stats['companies'],'color'=>'#5E122C'],
        ['icon'=>'bi-clock-history','label'=>'Empresas Pendientes','value'=>$stats['pending_companies'],'color'=>'#D97706'],
        ['icon'=>'bi-file-earmark-check','label'=>'Verificaciones DIAN Pendientes','value'=>$stats['pending_verifications'],'color'=>'#2563EB'],
        ['icon'=>'bi-globe-americas','label'=>'Solicitudes Internacionales','value'=>$stats['assisted_purchases'],'color'=>'#059669'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-sm-6 col-xl-4">
        <div class="vs-card h-100 p-2 border-0 shadow-sm" style="border-radius: 18px; border-left: 4px solid {{ $card['color'] }} !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:{{ $card['color'] }}18">
                    <i class="bi {{ $card['icon'] }} fs-4" style="color:{{ $card['color'] }}"></i>
                </div>
                <div>
                    <div class="text-muted text-sm fw-medium">{{ $card['label'] }}</div>
                    <div class="fw-bold fs-4 text-dark" style="font-family: 'Outfit', sans-serif;">{{ $card['value'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <!-- Últimos Usuarios Registrados -->
    <div class="col-lg-6">
        <div class="vs-card border-0 shadow-sm h-100 p-3" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 font-outfit">
                    <i class="bi bi-people text-primary me-2"></i>Últimos Usuarios Registrados
                </h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 text-xs fw-semibold">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted text-xs">
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Verificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $ru)
                        <tr>
                            <td class="fw-semibold text-dark text-sm">{{ $ru->name }}</td>
                            <td class="text-muted text-xs">{{ $ru->email }}</td>
                            <td>
                                <span class="badge badge-soft-primary rounded-pill px-2 py-1 text-xs">{{ ucfirst($ru->role) }}</span>
                            </td>
                            <td>
                                @if($ru->email_verified_at)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0 text-xs"><i class="bi bi-check"></i> Sí</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-0 text-xs">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay registros aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Últimas Empresas Registradas -->
    <div class="col-lg-6">
        <div class="vs-card border-0 shadow-sm h-100 p-3" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0 font-outfit">
                    <i class="bi bi-building text-primary me-2"></i>Últimas Empresas Registradas
                </h6>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 text-xs fw-semibold">Ver todas</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr class="text-muted text-xs">
                            <th>Empresa</th>
                            <th>País</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompanies as $rc)
                        <tr>
                            <td class="fw-semibold text-dark text-sm">{{ $rc->name }}</td>
                            <td>
                                <span class="badge bg-light border text-dark rounded-pill px-2 py-0 text-xs">
                                    {{ $rc->country === 'MX' ? '🇲🇽 México' : '🇨🇴 Colombia' }}
                                </span>
                            </td>
                            <td class="text-muted text-xs">{{ $rc->category ?: 'General' }}</td>
                            <td>
                                <span class="badge {{ $rc->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2 py-0 text-xs">
                                    {{ ucfirst($rc->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay empresas registradas aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="vs-card p-3 border-0 shadow-sm" style="border-radius: 18px;">
    <div class="card-body">
        <h6 class="fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;">Acceso rápido a gobernanza y administración</h6>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-people me-1"></i>Gestionar usuarios
            </a>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-building me-1"></i>Aprobación de empresas
            </a>
            <a href="{{ route('admin.fiscal_verifications.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-file-earmark-check me-1"></i>Verificaciones DIAN
            </a>
            <a href="{{ route('admin.assisted_purchases.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-globe-americas me-1"></i>Solicitudes Internacionales
            </a>
        </div>
    </div>
</div>
@endsection
