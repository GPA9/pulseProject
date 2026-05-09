<x-app-layout>
    <div class="container py-5 text-center" style="max-width: 580px;">

        {{-- Icono animado --}}
        <div class="mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle"
            style="width: 96px; height: 96px; background: rgba(16,185,129,0.15); border: 2px solid rgba(16,185,129,0.4);">
            <i class="bi bi-check-lg text-success" style="font-size: 3rem;"></i>
        </div>

        <h1 class="fw-bold mb-2" style="font-size:1.8rem;">¡Pago completado!</h1>
        <p class="text-muted mb-4" style="font-size:1rem;">
            Tu compra se ha procesado correctamente. ¡Gracias por apoyar la música independiente!
        </p>

        {{-- Mensajes flash --}}
        @if(session('ticket_sent'))
            <div class="alert border-0 mb-4 d-flex align-items-center gap-2"
                 style="background:rgba(16,185,129,0.12);color:#10b981;border-radius:12px;">
                <i class="bi bi-envelope-check-fill fs-5"></i>
                {{ session('ticket_sent') }}
            </div>
        @endif
        @if(session('ticket_error'))
            <div class="alert border-0 mb-4 d-flex align-items-center gap-2"
                 style="background:rgba(239,68,68,0.12);color:#f87171;border-radius:12px;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                {{ session('ticket_error') }}
            </div>
        @endif

        @if(!empty($orders) && count($orders) > 1)
            {{-- Multi-order summary (cart checkout) --}}
            <div class="card border-0 mb-4 text-start" style="background: var(--pulse-surface); border-radius: 16px;">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1.5px;">
                        Detalle del pedido
                    </h6>
                    @php $grandTotal = 0; @endphp
                    @foreach($orders as $o)
                        @php $grandTotal += $o->amount; @endphp
                        <div class="d-flex justify-content-between mb-2 pb-2" style="border-bottom:1px solid var(--pulse-border);">
                            <div>
                                <div class="text-white fw-semibold" style="font-size:0.9rem;">{{ $o->item_name }}</div>
                                <div class="text-muted" style="font-size:0.78rem;">× {{ $o->quantity ?? 1 }} ud.</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color:var(--pulse-primary);">{{ number_format($o->amount, 2, ',', '.') }} €</div>
                                <span class="badge bg-success" style="font-size:0.65rem;">Pagado</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between mt-3">
                        <span class="fw-bold text-white">Total</span>
                        <span class="fw-bold fs-5" style="color:var(--pulse-primary);">{{ number_format($grandTotal, 2, ',', '.') }} €</span>
                    </div>
                </div>
            </div>

        @elseif($order)
            {{-- Single order summary --}}
            <div class="card border-0 mb-4 text-start" style="background: var(--pulse-surface); border-radius: 16px;">
                <div class="card-body p-4">
                    <h6 class="text-muted fw-bold text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1.5px;">
                        Detalle del pedido</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Artículo</span>
                        <span class="text-white fw-semibold"
                            style="max-width:240px; text-align:right;">{{ $order->item_name }}</span>
                    </div>
                    @if(($order->quantity ?? 1) > 1)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cantidad</span>
                        <span class="text-white">× {{ $order->quantity }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total pagado</span>
                        <span class="fw-bold"
                            style="color: var(--pulse-primary);">{{ number_format($order->amount, 2, ',', '.') }} €</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Estado</span>
                        <span class="badge bg-success">Pagado</span>
                    </div>
                    @if($order->stripe_session_id)
                        <hr style="border-color: var(--pulse-border);">
                        <p class="text-muted mb-0" style="font-size:0.75rem;">
                            <i class="bi bi-receipt me-1"></i>Ref: {{ Str::limit($order->stripe_session_id, 30) }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── Acciones de ticket ─────────────────────────────────────────── --}}
        @php
            $isMulti   = !empty($orders) && count($orders) > 1;
            $orderIds  = $isMulti ? implode(',', collect($orders)->pluck('id')->toArray()) : null;
            $hasOrder  = $isMulti || $order;
        @endphp

        @if($hasOrder)
        <div class="d-flex gap-2 mb-4">

            {{-- Descargar ticket --}}
            @if($isMulti)
                <a href="{{ route('ticket.download-multiple', ['order_ids' => $orderIds]) }}"
                   target="_blank"
                   class="btn flex-fill py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                   style="background:linear-gradient(135deg,rgba(212,175,55,0.18),rgba(212,175,55,0.06));color:#d4af37;border:1px solid rgba(212,175,55,0.4);border-radius:12px;font-size:0.9rem;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Descargar PDF
                </a>
            @else
                <a href="{{ route('ticket.download', $order) }}"
                   target="_blank"
                   class="btn flex-fill py-3 fw-bold d-flex align-items-center justify-content-center gap-2"
                   style="background:linear-gradient(135deg,rgba(212,175,55,0.18),rgba(212,175,55,0.06));color:#d4af37;border:1px solid rgba(212,175,55,0.4);border-radius:12px;font-size:0.9rem;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Descargar PDF
                </a>
            @endif


        </div>
        @endif

        {{-- Navigation --}}
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ route('dashboard') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-grid me-2"></i>Mi panel
            </a>
            @if($order && $order->item_type === 'merch')
            <a href="{{ route('merch.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                <i class="bi bi-bag me-2"></i>Ver más merch
            </a>
            @else
            <a href="{{ route('concerts.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                <i class="bi bi-music-note-list me-2"></i>Ver más conciertos
            </a>
            @endif
        </div>
    </div>
</x-app-layout>