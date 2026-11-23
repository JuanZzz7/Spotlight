@extends('layouts.dashboard')
@section('title','Empresas - Admin')
@section('page-title','Gestión de Empresas')

@section('dashboard-content')
<div class="vs-card">
    <div class="card-body p-0">
<div class="row g-4">
    @forelse($companies as $company)
    <div class="col-md-6 col-lg-4">
        <div class="vs-card h-100 overflow-hidden p-0">
            {{-- Banner --}}
            <div style="height: 100px; position: relative; background: linear-gradient(to right, #f8fafc, #e2e8f0);">
                @if($company->banner)
                    <img src="{{ asset('storage/'.$company->banner) }}" class="w-100 h-100" style="object-fit: cover;">
                @endif
                <div class="position-absolute top-0 end-0 p-2">
                    <span class="badge {{ $company->status==='active'?'bg-success':($company->status==='pending'?'bg-warning':'bg-danger') }} rounded-pill px-3 shadow-sm">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
            </div>

            <div class="p-3 pt-4 position-relative">
                {{-- Logo overlapping --}}
                <div class="position-absolute" style="top: -35px; left: 15px;">
                    <div class="vs-card p-1 rounded-circle bg-white shadow-sm" style="width: 60px; height: 60px;">
                        <div class="w-100 h-100 rounded-circle bg-light overflow-hidden d-flex align-items-center justify-content-center">
                            @if($company->logo)
                                <img src="{{ asset('storage/'.$company->logo) }}" class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <i class="bi bi-building text-muted"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 150px;">{{ $company->name }}</h6>
                        <p class="text-muted text-xs mb-0">{{ $company->user->name ?? 'Sin dueño' }}</p>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-soft-primary text-dark text-xs fw-bold" style="background-color: #F6E7EB !important; color: #111827 !important; border: 1px solid rgba(127, 29, 63, 0.22);">{{ $company->category }}</span>
                    </div>
                </div>

                @if($company->camara_comercio_file || $company->rut_file || $company->rut_personal_file)
                <div class="mt-2 mb-3">
                    <p class="text-xs fw-semibold text-muted mb-1">Documentos adjuntos:</p>
                    <div class="d-flex flex-wrap gap-2">
                        @if($company->camara_comercio_file)
                        <button type="button" class="btn btn-sm btn-light border text-xs" data-bs-toggle="modal" data-bs-target="#docModal" onclick="showDocument('{{ asset('storage/' . $company->camara_comercio_file) }}', 'Cámara de Comercio - {{ $company->name }}')"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> Cám. Comercio</button>
                        @endif
                        @if($company->rut_file)
                        <button type="button" class="btn btn-sm btn-light border text-xs" data-bs-toggle="modal" data-bs-target="#docModal" onclick="showDocument('{{ asset('storage/' . $company->rut_file) }}', 'RUT Empresa - {{ $company->name }}')"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> RUT</button>
                        @endif
                        @if($company->rut_personal_file)
                        <button type="button" class="btn btn-sm btn-light border text-xs" data-bs-toggle="modal" data-bs-target="#docModal" onclick="showDocument('{{ asset('storage/' . $company->rut_personal_file) }}', 'RUT Personal - {{ $company->name }}')"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> RUT</button>
                        @endif
                    </div>
                </div>
                @endif

                @if($company->document_update_status === 'requested')
                <div class="mt-3 p-3 bg-white rounded shadow-sm border" style="border-left: 4px solid #f59e0b !important;">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                        <span class="fw-bold text-dark text-sm">Solicitud de Actualización</span>
                    </div>
                    <p class="text-xs text-muted mb-3 p-2 bg-light rounded fst-italic border">"{{ $company->document_update_reason }}"</p>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.companies.document_update_status', $company) }}" method="POST" class="flex-fill">
                            @csrf @method('PATCH')
                            <input type="hidden" name="document_update_status" value="granted">
                            <button class="btn btn-sm btn-outline-success w-100 fw-semibold text-xs py-1"><i class="bi bi-check-lg me-1"></i>Permitir edición</button>
                        </form>
                        <form action="{{ route('admin.companies.document_update_status', $company) }}" method="POST" class="flex-fill">
                            @csrf @method('PATCH')
                            <input type="hidden" name="document_update_status" value="null">
                            <button class="btn btn-sm btn-outline-danger w-100 fw-semibold text-xs py-1"><i class="bi bi-x-lg me-1"></i>Rechazar</button>
                        </form>
                    </div>
                </div>
                @endif

                <div class="d-flex gap-2 mt-3 border-top pt-3">
                    @if($company->status === 'pending')
                        <form action="{{ route('admin.companies.toggle', $company) }}" method="POST" class="flex-fill">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="active">
                            <button class="btn btn-sm btn-success w-100 fw-semibold"><i class="bi bi-check-circle me-1"></i>Aprobar</button>
                        </form>
                        <form action="{{ route('admin.companies.toggle', $company) }}" method="POST" class="flex-fill" onsubmit="return confirm('¿Estás seguro de rechazar esta empresa?');">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="inactive">
                            <button class="btn btn-sm btn-outline-danger w-100 fw-semibold"><i class="bi bi-x-circle me-1"></i>Rechazar</button>
                        </form>
                    @else
                        <form action="{{ route('admin.companies.toggle', $company) }}" method="POST" class="flex-fill">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $company->status === 'active' ? 'inactive' : 'active' }}">
                            <button class="btn btn-sm {{ $company->status==='active'?'btn-outline-warning':'btn-outline-success' }} w-100 fw-semibold">
                                <i class="bi {{ $company->status==='active'?'bi-pause':'bi-play' }} me-1"></i>
                                {{ $company->status==='active'?'Desactivar':'Activar' }}
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" onsubmit="return confirm('¿Eliminar empresa?')" class="flex-shrink-0">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <p class="text-muted mb-0">No hay empresas registradas.</p>
    </div>
    @endforelse
</div>
    </div>
</div>
<div class="mt-4 d-flex justify-content-center">{{ $companies->links() }}</div>

<!-- Document Modal -->
<div class="modal fade" id="docModal" tabindex="-1" aria-labelledby="docModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 bg-light">
        <h5 class="modal-title fw-bold" id="docModalLabel">Visor de Documentos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="height: 80vh; background: #e5e7eb;">
        <iframe id="docViewer" src="" width="100%" height="100%" style="border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
function showDocument(url, title) {
    document.getElementById('docModalLabel').innerText = title;
    document.getElementById('docViewer').src = url;
}
</script>
@endsection
