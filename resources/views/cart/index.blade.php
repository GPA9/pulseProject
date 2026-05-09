<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center g-4">

            {{-- ── Left: Cart Items ─────────────────────────────────── --}}
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="fw-bold mb-0" style="font-size:1.6rem;"><i class="bi bi-cart3 me-2" style="color:var(--pulse-primary);"></i>Mi Carrito</h1>
                    @if(!empty($cart))
                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Vaciar
                            </button>
                        </form>
                    @endif
                </div>

                @if(session('cart_error'))
                    <div class="alert border-0 mb-3" style="background:rgba(239,68,68,0.12);color:#f87171;">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('cart_error') }}
                    </div>
                @endif
                @if(session('cart_success'))
                    <div class="alert border-0 mb-3" style="background:rgba(16,185,129,0.12);color:#10b981;">
                        <i class="bi bi-check-circle me-2"></i>{{ session('cart_success') }}
                    </div>
                @endif

                @if(empty($cart))
                    {{-- Empty state --}}
                    <div class="text-center py-5" style="border:1px solid var(--pulse-border);border-radius:16px;background:var(--pulse-surface);">
                        <i class="bi bi-cart-x" style="font-size:4rem;color:var(--pulse-primary);opacity:0.4;"></i>
                        <h4 class="mt-3 mb-2 fw-bold">Tu carrito está vacío</h4>
                        <p class="text-muted mb-4">Añade entradas de concierto o productos de merchandising para empezar.</p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ route('concerts.index') }}" class="btn btn-primary px-4">
                                <i class="bi bi-ticket me-2"></i>Ver conciertos
                            </a>
                            <a href="{{ route('merch.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-bag me-2"></i>Ver merchandising
                            </a>
                        </div>
                    </div>

                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($cart as $idx => $ci)
                        <div class="card border-0" style="background:var(--pulse-surface);border-radius:14px;">
                            <div class="card-body p-3 d-flex align-items-center gap-3">

                                {{-- Icono / tipo --}}
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:54px;height:54px;background:rgba(239,225,181,0.08);border:1px solid rgba(239,225,181,0.15);">
                                    @if($ci['type'] === 'concert')
                                        <i class="bi bi-ticket-perforated" style="font-size:1.4rem;color:var(--pulse-primary);"></i>
                                    @else
                                        @if(!empty($ci['image']))
                                            <img src="{{ asset('storage/' . $ci['image']) }}" class="w-100 h-100 rounded-3" style="object-fit:cover;" alt="">
                                        @else
                                            <i class="bi bi-bag-fill" style="font-size:1.4rem;color:var(--pulse-primary);"></i>
                                        @endif
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if($ci['type'] === 'concert')
                                            <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.3);font-size:0.65rem;">ENTRADA</span>
                                        @else
                                            <span class="badge" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:1px solid rgba(245,158,11,0.3);font-size:0.65rem;">MERCH</span>
                                        @endif
                                        @if(!empty($ci['size']))
                                            <span class="badge" style="background:rgba(139,92,246,0.15);color:#a78bfa;border:1px solid rgba(139,92,246,0.3);font-size:0.65rem;">Talla: {{ $ci['size'] }}</span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold text-white" style="font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $ci['name'] }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.8rem;">
                                        {{ number_format($ci['price'], 2, ',', '.') }} € / ud.
                                    </div>
                                </div>

                                {{-- Cantidad stepper --}}
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <form method="POST" action="{{ route('cart.update') }}" class="qty-form-{{ $idx }} d-flex align-items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="index" value="{{ $idx }}">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            onclick="changeQty({{ $idx }}, -1)">−</button>
                                        <input type="number" name="quantity" id="qtyField{{ $idx }}"
                                            value="{{ $ci['quantity'] }}" min="1" max="20"
                                            class="form-control form-control-sm text-center bg-transparent border-secondary text-white fw-bold"
                                            style="width:48px;"
                                            onchange="submitQty({{ $idx }})">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:30px;height:30px;"
                                            onclick="changeQty({{ $idx }}, 1)">+</button>
                                    </form>
                                </div>

                                {{-- Subtotal + eliminar --}}
                                <div class="text-end flex-shrink-0 ms-2">
                                    <div class="fw-bold" style="color:var(--pulse-primary);font-size:1rem;">
                                        {{ number_format($ci['price'] * $ci['quantity'], 2, ',', '.') }} €
                                    </div>
                                    <form method="POST" action="{{ route('cart.remove') }}" class="mt-1">
                                        @csrf
                                        <input type="hidden" name="index" value="{{ $idx }}">
                                        <button type="submit" class="btn btn-link p-0 text-danger" style="font-size:0.78rem;">
                                            <i class="bi bi-x-lg"></i> Quitar
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Right: Order Summary ──────────────────────────────── --}}
            @if(!empty($cart))
            @php
                $subtotal = collect($cart)->sum(fn($ci) => $ci['price'] * $ci['quantity']);
                $commission = $subtotal * 0.05;
                $total = $subtotal;
            @endphp
            <div class="col-12 col-lg-4">
                <div class="card border-0 sticky-top" style="background:var(--pulse-surface);border-radius:16px;top:80px;">
                    <div style="height:4px;background:linear-gradient(90deg,var(--pulse-primary),#c9a84c);border-radius:16px 16px 0 0;"></div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumen del pedido</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="text-white">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Comisión Pulse (5%)</span>
                            <span class="text-muted">{{ number_format($commission, 2, ',', '.') }} €</span>
                        </div>
                        <hr style="border-color:var(--pulse-border);">
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5 text-white">Total</span>
                            <span class="fw-bold fs-4" style="color:var(--pulse-primary);">{{ number_format($total, 2, ',', '.') }} €</span>
                        </div>

                        {{-- Artículos en el carrito --}}
                        <div class="mb-4" style="font-size:0.8rem;">
                            @foreach($cart as $ci)
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span class="text-truncate me-2" style="max-width:170px;">{{ $ci['name'] }}</span>
                                    <span class="flex-shrink-0">×{{ $ci['quantity'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagar --}}
                        <form method="POST" action="{{ route('cart.checkout') }}">
                            @csrf
                            <button type="submit"
                                class="btn w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                                style="background:var(--pulse-primary);color:#121212;border-radius:12px;border:none;transition:opacity 0.15s;"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z" />
                                </svg>
                                Pagar con Stripe
                            </button>
                        </form>

                        <p class="text-center text-muted mt-3 mb-0" style="font-size:0.75rem;">
                            <i class="bi bi-shield-lock me-1"></i>Pago 100% seguro
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <script>
        function changeQty(idx, delta) {
            const input = document.getElementById('qtyField' + idx);
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > 20) val = 20;
            input.value = val;
            // Auto-submit the form
            input.closest('form').submit();
        }

        function submitQty(idx) {
            document.getElementById('qtyField' + idx).closest('form').submit();
        }
    </script>
</x-app-layout>
