<x-app-layout>
    <div class="container py-5" style="max-width: 560px;">

        {{-- Cabecera --}}
        <div class="mb-4">
            <a href="{{ $type === 'merch' ? route('merch.index') : route('concerts.index') }}"
               class="btn btn-link text-muted p-0 d-flex align-items-center gap-2" style="font-size:0.9rem; text-decoration:none;">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        @if(session('cart_error'))
            <div class="alert alert-danger border-0 mb-3" style="background:rgba(239,68,68,0.12);color:#f87171;">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('cart_error') }}
            </div>
        @endif
        @if(session('cart_success'))
            <div class="alert alert-success border-0 mb-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('cart_success') }}
            </div>
        @endif
        @error('quantity')
            <div class="alert alert-danger border-0 mb-3" style="background:rgba(239,68,68,0.12);color:#f87171;">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
            </div>
        @enderror

        {{-- Tarjeta del artículo --}}
        <div class="card border-0 mb-4" style="background: var(--pulse-surface); border-radius: 16px; overflow:hidden;">
            <div style="height: 6px; background: linear-gradient(90deg, var(--pulse-primary), #c9a84c);"></div>
            <div class="card-body p-4">

                @if($type === 'concert')
                    <div class="d-flex gap-3 align-items-start">
                        {{-- Fecha destacada --}}
                        <div class="text-center rounded-3 p-3 d-flex flex-column align-items-center justify-content-center"
                            style="background: rgba(239,225,181,0.1); border: 1px solid rgba(239,225,181,0.2); min-width: 72px;">
                            <span class="text-primary fw-bold"
                                style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px;">
                                {{ $item->date->locale('es')->isoFormat('MMM') }}
                            </span>
                            <span class="text-white fw-bold" style="font-size:2rem; line-height:1;">
                                {{ $item->date->format('d') }}
                            </span>
                        </div>
                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <span class="badge text-black fw-bold mb-1"
                                style="background: var(--pulse-primary); font-size:0.7rem;">
                                <i class="bi bi-ticket me-1"></i>ENTRADA
                            </span>
                            <h2 class="fw-bold text-white mb-1" style="font-size:1.2rem;">
                                {{ $item->musicianProfile->stage_name ?? 'Artista' }}
                            </h2>
                            <p class="text-muted mb-0" style="font-size:0.9rem;">
                                <i class="bi bi-geo-alt me-1"></i>{{ $item->venue }}, {{ $item->city }}
                            </p>
                            <p class="text-muted mb-0" style="font-size:0.85rem;">
                                <i class="bi bi-clock me-1"></i>{{ $item->date->format('H:i') }}h
                            </p>
                            @if($item->capacity_available !== null)
                                <p class="mt-2 mb-0" style="font-size:0.82rem;color:var(--pulse-primary);">
                                    <i class="bi bi-people me-1"></i>
                                    Disponibles: <strong>{{ $item->capacity_available }}</strong> / {{ $item->capacity }}
                                </p>
                            @endif
                        </div>
                    </div>

                @elseif($type === 'merch')
                    <div class="d-flex gap-3 align-items-center">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:72px; height:72px; background: rgba(239,225,181,0.1); flex-shrink:0;">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" class="w-100 h-100 rounded-3" style="object-fit:cover;" alt="">
                            @else
                                <i class="bi bi-bag-fill fs-2" style="color: var(--pulse-primary);"></i>
                            @endif
                        </div>
                        <div>
                            <span class="badge text-black fw-bold mb-1"
                                style="background: var(--pulse-primary); font-size:0.7rem;">
                                <i class="bi bi-bag me-1"></i>MERCH
                            </span>
                            <h2 class="fw-bold text-white mb-1" style="font-size:1.2rem;">{{ $item->name }}</h2>
                            <p class="text-muted mb-0" style="font-size:0.9rem;">
                                {{ $item->musicianProfile->stage_name ?? 'Artista' }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Selector de cantidad --}}
        <div class="card border-0 mb-4" style="background: var(--pulse-surface); border-radius: 16px;">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1.5px;">Cantidad</h6>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="qtyMinus" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:40px;height:40px;font-size:1.2rem;">−</button>
                    <input type="number" id="qtyInput" value="1" min="1" max="{{ $type === 'concert' ? ($item->capacity_available ?? 20) : 20 }}"
                        class="form-control text-center fw-bold text-white bg-transparent border-0"
                        style="width:60px;font-size:1.3rem;">
                    <button type="button" id="qtyPlus" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center"
                        style="width:40px;height:40px;font-size:1.2rem;">+</button>
                    <span class="text-muted" style="font-size:0.85rem;">
                        @if($type === 'concert' && $item->capacity_available !== null)
                            máx. {{ $item->capacity_available }}
                        @else
                            máx. 20
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Selector de talla (solo para merch) --}}
        @if($type === 'merch' && $item->sizes && count($item->sizes) > 0)
            <div class="card border-0 mb-4" style="background: var(--pulse-surface); border-radius: 16px;">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1.5px;">Talla</h6>
                    <div class="d-flex flex-wrap gap-2" id="sizeButtons">
                        @foreach($item->sizes as $index => $size)
                            <button type="button"
                                class="btn btn-outline-secondary size-btn py-2 px-3 {{ $index === 0 ? 'active' : '' }}"
                                style="border-radius:8px;font-size:0.9rem;min-width:52px;"
                                data-size="{{ $size }}"
                                onclick="selectSize(this)">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                    {{-- Hidden inputs (one per form) --}}
                    <input type="hidden" id="sizeCartInput"   value="{{ $item->sizes[0] ?? '' }}">
                    <input type="hidden" id="sizeStripeInput" value="{{ $item->sizes[0] ?? '' }}">
                </div>
            </div>
        @endif

        {{-- Desglose de precio --}}
        <div class="card border-0 mb-4" style="background: var(--pulse-surface); border-radius: 16px;">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1.5px;">
                    Desglose</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Precio unitario</span>
                    <span class="text-white" id="unitPriceDisplay">{{ number_format($item->price, 2, ',', '.') }} €</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Cantidad</span>
                    <span class="text-white" id="qtyDisplay">1</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Comisión Pulse (5%)</span>
                    <span class="text-muted" id="commDisplay">{{ number_format($item->price * 0.05, 2, ',', '.') }} €</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-muted" style="font-size:0.8rem;">
                    <span><i class="bi bi-music-note me-1"></i>Para el artista (95%)</span>
                    <span id="artistDisplay">{{ number_format($item->price * 0.95, 2, ',', '.') }} €</span>
                </div>
                <hr style="border-color: var(--pulse-border);">
                <div class="d-flex justify-content-between">
                    <span class="text-white fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-4" id="totalDisplay"
                        style="color: var(--pulse-primary);">{{ number_format($item->price, 2, ',', '.') }} €</span>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="d-flex flex-column gap-3">

            {{-- Añadir al carrito --}}
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="item_type" value="{{ $type }}">
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="quantity" id="cartQty" value="1">
                <input type="hidden" name="size" id="cartSizeField" value="">
                <button type="submit"
                    class="btn w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                    style="background: rgba(239,225,181,0.1); color: var(--pulse-primary); border: 1px solid rgba(239,225,181,0.3); border-radius: 12px; transition: all 0.15s;"
                    onmouseover="this.style.background='rgba(239,225,181,0.18)'"
                    onmouseout="this.style.background='rgba(239,225,181,0.1)'">
                    <i class="bi bi-cart-plus fs-5"></i>Añadir al carrito
                </button>
            </form>

            {{-- Pagar ahora con Stripe --}}
            <form method="POST" action="{{ route('checkout.stripe-session') }}">
                @csrf
                <input type="hidden" name="item_type" value="{{ $type }}">
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="quantity" id="stripeQty" value="1">
                <input type="hidden" name="size" id="stripeSizeField" value="">

                <button type="submit"
                    class="btn w-100 py-3 fw-bold fs-5 d-flex align-items-center justify-content-center gap-2"
                    style="background: var(--pulse-primary); color: #121212; border-radius: 12px; border: none; transition: transform 0.15s, opacity 0.15s;"
                    onmouseover="this.style.opacity='0.9'; this.style.transform='scale(1.01)'"
                    onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z" />
                    </svg>
                    Pagar ahora
                </button>
            </form>
        </div>

        <p class="text-center text-muted mt-3" style="font-size:0.8rem;">
            <i class="bi bi-shield-lock me-1"></i>Pago 100% seguro procesado por Stripe &nbsp;·&nbsp; No guardamos datos
            de tarjeta
        </p>
    </div>

    <script>
        const unitPrice = {{ $item->price }};
        const maxQty    = {{ $type === 'concert' ? ($item->capacity_available ?? 20) : 20 }};
        let qty = 1;

        function fmt(n) {
            return n.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }

        function updateDisplay() {
            const total   = unitPrice * qty;
            const comm    = total * 0.05;
            const artist  = total * 0.95;

            document.getElementById('qtyDisplay').textContent   = qty;
            document.getElementById('commDisplay').textContent  = fmt(comm);
            document.getElementById('artistDisplay').textContent= fmt(artist);
            document.getElementById('totalDisplay').textContent = fmt(total);
            document.getElementById('qtyInput').value          = qty;
            document.getElementById('cartQty').value           = qty;
            document.getElementById('stripeQty').value         = qty;
        }

        document.getElementById('qtyMinus').addEventListener('click', () => {
            if (qty > 1) { qty--; updateDisplay(); }
        });

        document.getElementById('qtyPlus').addEventListener('click', () => {
            if (qty < maxQty) { qty++; updateDisplay(); }
        });

        document.getElementById('qtyInput').addEventListener('change', (e) => {
            let v = parseInt(e.target.value);
            if (isNaN(v) || v < 1) v = 1;
            if (v > maxQty) v = maxQty;
            qty = v;
            updateDisplay();
        });

        // ── Selector de talla ────────────────────────────────────────────
        function selectSize(btn) {
            // Visual: toggle active
            document.querySelectorAll('#sizeButtons .size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Update hidden fields in both forms
            const size = btn.dataset.size;
            const cartField   = document.getElementById('cartSizeField');
            const stripeField = document.getElementById('stripeSizeField');
            if (cartField)   cartField.value   = size;
            if (stripeField) stripeField.value = size;
        }

        // Init: set first size on load if any size buttons exist
        document.addEventListener('DOMContentLoaded', function() {
            const firstBtn = document.querySelector('#sizeButtons .size-btn');
            if (firstBtn) selectSize(firstBtn);
        });
    </script>
</x-app-layout>