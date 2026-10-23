@extends('layouts.dashboard')
@section('title','Usuarios - Admin')
@section('page-title','Gestión de Usuarios')

@section('dashboard-content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="?role=all" class="btn {{ $role==='all'?'btn-primary':'btn-outline-primary' }} rounded-pill px-3 py-1 text-sm">
            Todos <span class="badge bg-white text-dark ms-1">{{ $counts['all'] ?? 0 }}</span>
        </a>
        <a href="?role=user" class="btn {{ $role==='user'?'btn-primary':'btn-outline-primary' }} rounded-pill px-3 py-1 text-sm">
            Usuarios <span class="badge bg-white text-dark ms-1">{{ $counts['user'] ?? 0 }}</span>
        </a>
        <a href="?role=business" class="btn {{ $role==='business'?'btn-primary':'btn-outline-primary' }} rounded-pill px-3 py-1 text-sm">
            Empresarios <span class="badge bg-white text-dark ms-1">{{ $counts['business'] ?? 0 }}</span>
        </a>
        <a href="?role=admin" class="btn {{ $role==='admin'?'btn-primary':'btn-outline-primary' }} rounded-pill px-3 py-1 text-sm">
            Admins <span class="badge bg-white text-dark ms-1">{{ $counts['admin'] ?? 0 }}</span>
        </a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
        <input type="hidden" name="role" value="{{ $role }}">
        <div class="input-group input-group-sm" style="width: 250px;">
            <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control rounded-start-pill" placeholder="Buscar por nombre o correo...">
            <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="bi bi-search"></i></button>
        </div>
        @if(!empty($search))
            <a href="?role={{ $role }}" class="btn btn-sm btn-light border rounded-pill">Limpiar</a>
        @endif
    </form>
</div>

<div class="vs-card border-0 shadow-sm" style="border-radius: 18px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Verificación</th>
                        <th>Registro</th>
                        <th class="pe-4 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-4 fw-semibold text-dark">{{ $user->name }}</td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td><span class="badge badge-soft-primary rounded-pill px-3 py-1">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        <span class="badge {{ $user->active ? 'badge-soft-success' : 'badge-soft-danger' }} rounded-pill px-3 py-1">
                            {{ $user->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 text-xs">
                                <i class="bi bi-patch-check-fill me-1"></i>Verificado
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1 text-xs">
                                <i class="bi bi-clock-history me-1"></i>Pendiente
                            </span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex gap-1">
                            {{-- Verificar cuenta si no está verificada --}}
                            @if(!$user->email_verified_at)
                                <form action="{{ route('admin.users.verify', $user) }}" method="POST" onsubmit="return confirm('¿Confirmar y verificar manualmente la cuenta de este usuario?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" title="Verificar cuenta de correo">
                                        <i class="bi bi-patch-check me-1"></i>Verificar
                                    </button>
                                </form>
                            @endif

                            {{-- Alternar activo / inactivo --}}
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $user->active?'btn-outline-warning':'btn-outline-success' }} rounded-pill px-2 py-1" title="{{ $user->active ? 'Desactivar acceso' : 'Activar acceso' }}">
                                    <i class="bi {{ $user->active?'bi-pause-circle':'bi-play-circle' }} me-1"></i>{{ $user->active ? 'Pausar' : 'Activar' }}
                                </button>
                            </form>

                            {{-- Eliminar cuenta (sin opción de edición) --}}
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar permanentemente este usuario? Esta acción es irreversible.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Eliminar cuenta">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No se encontraron usuarios en la base de datos con los criterios seleccionados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-4 d-flex justify-content-center">{{ $users->appends(request()->query())->links() }}</div>
@endsection
