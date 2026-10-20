@extends('layouts.dashboard')

@section('title', 'Tutoriales de Formalización - Spotlight')
@section('page-title', 'Ruta de Formalización Empresarial')

@section('dashboard-content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-sm">
                    <li class="breadcrumb-item"><a href="{{ route('business.dashboard') }}" class="text-decoration-none text-muted">Panel</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Tutoriales de Formalización</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-mortarboard-fill" style="color: #7F1D3F;"></i>
                <span>Centro de Tutoriales: Cómo Formalizar tu Negocio</span>
            </h4>
            <p class="text-muted small mb-0 mt-1">
                Guía educativa oficial, tutoriales en video y pasos recomendados para pasar de Emprendimiento Local a Empresa Registrada.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('business.company.edit') }}" class="btn btn-spotlight rounded-pill px-3 py-2 text-sm fw-semibold">
                <i class="bi bi-pencil-square me-1"></i> Mi Empresa / Aspecto Legal
            </a>
            <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 text-sm fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Volver al Panel
            </a>
        </div>
    </div>

    @include('business.partials.formalization-tutorials')
</div>
@endsection
