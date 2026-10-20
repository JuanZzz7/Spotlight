@extends('layouts.dashboard')
@section('title','Carrito - Spotlight')

@section('page-title')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">
    <div>
        <div class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">PORTAL COMPRADOR / LA ALDEA DIGITAL / CARRITO DE COMPRAS</div>
        <div class="mb-2"><span class="badge badge-coral" style="font-size: 0.65rem; font-weight: 800; letter-spacing: 0.5px;"><i class="bi bi-bag-check-fill me-1"></i> {{ count($items ?? []) }} ARTÍCULOS RESERVADOS</span></div>
        <h2 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; letter-spacing: -0.5px; font-size: 2.2rem;">Tu Carrito de <span class="text-gradient-purple">Compras</span></h2>
        <p class="text-muted mb-0" style="font-size: 0.85rem; max-width: 500px;">Revisa tus productos seleccionados antes de proceder al pago seguro y apoyar a la comunidad de emprendedores.</p>
    </div>
    
    <div class="d-flex align-items-center mt-3 mt-md-0 d-none d-lg-flex">
        <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm border">
            <div class="d-flex align-items-center">
                <span class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 24px; height: 24px; background: var(--sl-primary, #7F1D3F); font-size: 0.7rem;">1</span>
                <span class="ms-2 fw-bold text-dark" style="font-size: 0.75rem;">Carrito</span>
            </div>
            <div class="mx-2" style="width: 30px; height: 2px; background: linear-gradient(90deg, var(--sl-primary, #7F1D3F), #e2e8f0);"></div>
            <div class="d-flex align-items-center opacity-50">
                <span class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted fw-bold border" style="width: 24px; height: 24px; font-size: 0.7rem;">2</span>
                <span class="ms-2 fw-bold text-muted" style="font-size: 0.75rem;">Envío</span>
            </div>
            <div class="mx-2" style="width: 30px; height: 2px; background: #e2e8f0;"></div>
            <div class="d-flex align-items-center opacity-50">
                <span class="rounded-circle d-flex align-items-center justify-content-center bg-light text-muted fw-bold border" style="width: 24px; height: 24px; font-size: 0.7rem;">3</span>
                <span class="ms-2 fw-bold text-muted" style="font-size: 0.75rem;">Pago Seguro</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dashboard-content')
@if(empty($items))
    <div class="text-center py-5 mt-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 120px; height: 120px; background: var(--sl-primary-soft, #F6E7EB);">
            <i class="bi bi-cart-x" style="font-size: 4rem; color: var(--sl-primary, #7F1D3F);"></i>
        </div>
        <h3 class="fw-bold text-dark mt-3" style="font-family: 'Outfit', sans-serif;">Tu carrito está vacío</h3>
        <p class="text-muted fs-5 mb-4">Explora la plataforma y agrega productos a tu carrito.</p>
        <a href="{{ route('user.dashboard') }}" class="btn btn-spotlight btn-lg px-5 shadow-sm rounded-pill fw-bold">Explorar Marketplace</a>
    </div>
@else
<div class="row g-4 mt-2">
    <!-- COLUMNA IZQUIERDA (PRODUCTOS Y EXTRAS) -->
    <div class="col-lg-8">
        
        <div class="d-none d-md-flex text-muted fw-bold text-uppercase mb-3 px-4" style="font-size: 0.65rem; letter-spacing: 1px;">
            <div class="col-6">PRODUCTO & TIENDA</div>
            <div class="col-2 text-center">PRECIO</div>
            <div class="col-2 text-center">CANTIDAD</div>
            <div class="col-2 text-end pe-4">SUBTOTAL/ACCIÓN</div>
        </div>

        <div class="d-flex flex-column gap-3 mb-4">
            @foreach($items as $item)
            <div class="vs-card border-0 shadow-sm bg-white p-3 p-md-4 d-flex flex-column flex-md-row align-items-md-center position-relative" style="border-radius: 1.5rem;">
                <!-- Product Info -->
                <div class="col-12 col-md-6 d-flex align-items-center gap-3 mb-3 mb-md-0">
                    <div class="position-relative flex-shrink-0">
                        <img src="{{ $item['product']->image_url ?? 'https://ui-avatars.com/api/?name='.urlencode($item['product']->name).'&background=f1f5f9' }}" width="80" height="80" class="rounded-4" style="object-fit:cover; background: #f8fafc; padding: 5px;">
                        <span class="position-absolute bottom-0 end-0 translate-middle-x mb-n2 rounded-circle shadow-sm" style="width: 14px; height: 14px; background: var(--sl-accent, #D98C9A); border: 2px solid white;"></span>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            @if($loop->first)
                                <span class="badge badge-cyan" style="font-size: 0.55rem; font-weight: 800;">ENVÍO GRATIS</span>
                                <span class="badge bg-light text-primary border" style="font-size: 0.55rem;">Garantía 12m</span>
                            @else
                                <span class="badge badge-purple" style="font-size: 0.55rem; font-weight: 800;">ALDEA DIGITAL</span>
                                <span class="badge bg-light text-success border" style="font-size: 0.55rem;">Fresco Hoy</span>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mb-1 text-truncate" style="max-width: 200px;">{{ $item['product']->name }}</h6>
                        <div class="d-flex align-items-center text-muted" style="font-size: 0.65rem;">
                            <i class="bi bi-shop text-danger me-1"></i> {{ $item['product']->company->name }} <span class="ms-1 px-1 bg-light rounded text-muted">D.O. Protegida</span>
                        </div>
                    </div>
                </div>

                <!-- Price -->
                <div class="col-4 col-md-2 text-md-center mb-2 mb-md-0">
                    <div class="fw-bold text-dark fs-6 lh-1">${{ number_format($item['product']->price,0,',','.') }}</div>
                    <div class="text-muted fw-bold" style="font-size: 0.6rem;">COP</div>
                </div>

                <!-- Quantity -->
                <div class="col-4 col-md-2 d-flex justify-content-md-center mb-2 mb-md-0">
                    <form action="{{ route('user.cart.update', $item['product']) }}" method="POST" class="m-0">
                        @csrf @method('PATCH')
                        <div class="input-group border rounded-pill overflow-hidden shadow-sm bg-white" style="width: 90px; height: 32px;">
                            <button type="button" class="btn btn-light border-0 px-2 py-0 text-muted fw-bold" onclick="this.parentNode.querySelector('input[type=number]').stepDown(); this.form.submit()">-</button>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" class="form-control text-center border-0 p-0 fw-bold bg-white" style="font-size: 0.8rem;" onchange="this.form.submit()">
                            <button type="button" class="btn btn-light border-0 px-2 py-0 text-muted fw-bold" onclick="this.parentNode.querySelector('input[type=number]').stepUp(); this.form.submit()">+</button>
                        </div>
                    </form>
                </div>

                <!-- Subtotal / Remove -->
                <div class="col-4 col-md-2 d-flex align-items-center justify-content-end gap-3">
                    <div class="text-end">
                        <div class="fw-bold fs-5 lh-1" style="color: var(--sl-primary, #7F1D3F);">${{ number_format($item['subtotal'],0,',','.') }}</div>
                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">COP</div>
                    </div>
                    <form action="{{ route('user.cart.remove', $item['product']) }}" method="POST" class="m-0 ms-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm text-muted bg-light border-0 rounded-circle d-flex align-items-center justify-content-center hover-danger shadow-sm" style="width: 32px; height: 32px;"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Despacho consolidado y Cupones --}}
        <div class="vs-card border-0 shadow-sm bg-white p-3 p-md-4 rounded-4 d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; background: var(--sl-primary, #7F1D3F); color: white; flex-shrink: 0;">
                <i class="bi bi-truck fs-5"></i>
            </div>
            <div class="flex-fill">
                <h6 class="fw-bold text-dark mb-1">Despacho Consolidado Aldea Digital</h6>
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Tus pedidos de tiendas distintas viajan en un solo flete ecológico sin costo adicional.</p>
            </div>
            <span class="badge badge-cyan px-3 py-2 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">RUTA ACTIVA</span>
        </div>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="vs-card border-0 shadow-sm rounded-4 p-4 h-100" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-ticket-perforated" style="color: var(--sl-primary, #7F1D3F);"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Cupón de Descuento</h6>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.75rem;">Ingresa el código promocional emitido por la Aldea Digital o sus emprendedores.</p>
                    <div class="input-group bg-white rounded-pill p-1 border shadow-sm">
                        <input type="text" class="form-control border-0 bg-transparent fw-medium ps-3" style="font-size: 0.75rem; box-shadow: none;" placeholder="Código: ej. SPOTLIGHT20">
                        <button class="btn btn-dark rounded-pill fw-bold text-white px-3 py-1" style="font-size: 0.75rem;">Aplicar</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vs-card border-0 shadow-sm rounded-4 p-4 h-100" style="background: var(--sl-primary-soft, #F6E7EB);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill" style="color: var(--sl-primary, #7F1D3F);"></i>
                            <h6 class="fw-bold mb-0" style="color: var(--sl-primary, #7F1D3F); font-size: 0.85rem;">Spotlight Puntos</h6>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.75rem;">1.450 <span class="text-muted fw-semibold">pts disponibles</span></div>
                    </div>
                    <div class="bg-white rounded-4 p-3 d-flex justify-content-between align-items-center shadow-sm">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Canjear 500 pts</div>
                            <div class="fw-bold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.65rem;">Ahorra $5.000 COP al instante</div>
                        </div>
                        <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" checked style="width: 40px; height: 20px; border-color: var(--sl-primary, #7F1D3F); background-color: var(--sl-primary, #7F1D3F);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA (RESUMEN) -->
    <div class="col-lg-4">
        <div class="vs-card shadow-sm bg-white" style="border-radius: 1.5rem; position: sticky; top: 100px; border-top: 5px solid transparent; background-image: linear-gradient(white, white), linear-gradient(90deg, var(--sl-primary, #7F1D3F), var(--sl-accent, #D98C9A)); background-origin: border-box; background-clip: padding-box, border-box;">
            <div class="card-body p-4 p-xl-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="fw-bold text-dark mb-0" style="font-family: 'Outfit', sans-serif;">Resumen del pedido</h4>
                    <i class="bi bi-receipt fs-4" style="color: var(--sl-primary, #7F1D3F);"></i>
                </div>
                
                @php
                    // Simulación visual de descuento con puntos para el UI (fijo)
                    $puntosDescuento = 5000;
                    $finalTotal = max(0, $total - $puntosDescuento);
                @endphp
                
                <div class="d-flex justify-content-between align-items-end mb-3">
                    <span class="text-dark fw-medium" style="font-size: 0.85rem;">Subtotal de productos<br><span class="text-muted" style="font-size: 0.75rem;">({{ count($items) }} uds)</span></span>
                    <div class="text-end">
                        <div class="text-dark fw-bold fs-6 lh-1">${{ number_format($total,0,',','.') }}</div>
                        <div class="text-dark fw-bold" style="font-size: 0.6rem;">COP</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-medium" style="font-size: 0.85rem;">Envío Express <i class="bi bi-info-circle ms-1"></i></span>
                    <span class="badge badge-cyan" style="font-size: 0.7rem; font-weight: 800; padding: 6px 10px;">GRATIS</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded" style="background: var(--sl-primary-soft, #F6E7EB);">
                    <span class="fw-bold d-flex align-items-center" style="font-size: 0.85rem; color: var(--sl-primary, #7F1D3F);">
                        <i class="bi bi-star-fill me-2" style="color: var(--sl-primary, #7F1D3F);"></i> Descuento Puntos
                    </span>
                    <div class="text-end">
                        <div class="fw-bold lh-1" style="color: var(--sl-primary, #7F1D3F); font-size: 0.95rem;">-${{ number_format($puntosDescuento,0,',','.') }}</div>
                        <div class="fw-bold" style="color: var(--sl-primary, #7F1D3F); font-size: 0.6rem;">COP</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
                    <span class="text-muted" style="font-size: 0.75rem;">Impuestos locales (IVA inc.)</span>
                    <span class="text-muted fw-bold" style="font-size: 0.75rem;">$0 COP</span>
                </div>
                
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <div>
                        <span class="fw-bold fs-5 text-dark d-block">Total a pagar</span>
                        <span class="text-muted" style="font-size: 0.6rem; font-weight: 600;">Precios finales garantizados</span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="color: var(--sl-primary, #7F1D3F) !important; font-size: 2rem; line-height: 1; font-family: 'Outfit', sans-serif;">${{ number_format($finalTotal,0,',','.') }}</div>
                        <div class="text-dark fw-bold" style="font-size: 0.7rem;">COP</div>
                    </div>
                </div>
                
                <a href="{{ route('user.cart.checkout') }}" class="btn btn-spotlight btn-lg w-100 py-3 shadow-sm d-flex justify-content-between align-items-center px-4 rounded-pill fw-bold text-white mb-2" style="font-size: 0.95rem;">
                    <span>Continuar al pago</span>
                    <i class="bi bi-arrow-right fs-5"></i>
                </a>
                
                <a href="{{ route('user.dashboard') }}" class="btn btn-light w-100 py-3 rounded-pill fw-bold text-muted border shadow-sm d-flex justify-content-center align-items-center gap-2 mb-4 hover-bg" style="font-size: 0.85rem; background: linear-gradient(to bottom, #fff, #f8fafc);">
                    <i class="bi bi-shop"></i> Seguir explorando la Aldea Digital
                </a>
                
                <div class="bg-light rounded-4 p-3 mb-4 d-flex align-items-center gap-3 border" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm bg-white" style="width: 32px; height: 32px; color: var(--sl-primary, #7F1D3F); flex-shrink: 0;">
                        <i class="bi bi-award-fill" style="color: var(--sl-primary, #7F1D3F); font-size: 1.2rem;"></i>
                    </div>
                    <div style="font-size: 0.7rem; line-height: 1.3;" class="text-muted">
                        <span class="fw-bold text-success">+320 Puntos</span> a ganar con esta compra para tu nivel Platino.
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-2 text-muted" style="font-size: 0.65rem;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check" style="color: var(--sl-primary, #7F1D3F); font-size: 1rem;"></i>
                        <span>Compra 100% Protegida por <strong>SPOTLIGHT Aldea Digital</strong></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-heart-fill text-warning" style="font-size: 1rem;"></i>
                        <span>Comercio Justo Regional directo al artesano</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card text-primary" style="font-size: 1rem;"></i>
                        <span>PSE, Tarjetas de Crédito y Pago Contra Entrega</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    
    .form-check-input:checked {
        background-color: var(--sl-primary, #7F1D3F);
        border-color: var(--sl-primary, #7F1D3F);
    }
    .form-check-input:focus {
        border-color: var(--sl-primary, #7F1D3F);
        box-shadow: 0 0 0 0.25rem rgba(127, 29, 63, 0.2);
    }
    
    .hover-danger { transition: all 0.2s ease; }
    .hover-danger:hover { background-color: #ef4444 !important; color: white !important; }
    
    .hover-bg { transition: all 0.2s ease; }
    .hover-bg:hover { filter: brightness(0.95); }
</style>
@endsection
