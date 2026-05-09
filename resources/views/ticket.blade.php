<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket — Pulse</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #d4af37;
            --bg: #0e0e0e;
            --surface: #1a1a1a;
            --border: rgba(255,255,255,0.1);
            --text: #f5f5f5;
            --muted: #888;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 2rem;
        }

        .page-wrap { max-width: 680px; margin: 0 auto; }

        /* ── Action bar (hidden in print) ── */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.2rem;
            background: rgba(255,255,255,0.06);
            color: var(--text);
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid var(--border);
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .btn-back:hover { opacity: 0.75; color: var(--text); }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1.4rem;
            background: var(--gold);
            color: #111;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s;
        }
        .btn-print:hover { opacity: 0.85; }

        /* ── Ticket card ── */
        .ticket {
            background: var(--surface);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 60px rgba(212,175,55,0.08), 0 20px 60px rgba(0,0,0,0.6);
        }

        .ticket-header {
            background: linear-gradient(135deg, #1a1500 0%, #2d2000 50%, #1a1200 100%);
            border-bottom: 1px solid rgba(212,175,55,0.3);
            padding: 1.75rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand { display: flex; align-items: center; gap: 1rem; }
        .brand-logo {
            height: 52px;
            width: auto;
            border-radius: 8px;
            filter: drop-shadow(0 0 8px rgba(212,175,55,0.4));
        }
        .brand-text { }
        .brand-name {
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--gold);
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .brand-sub {
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .ticket-status { display: flex; flex-direction: column; align-items: flex-end; gap: 0.3rem; }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.4);
            color: #10b981;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; }
        .ticket-date { font-size: 0.72rem; color: var(--muted); }

        /* Perforated divider */
        .perforated {
            position: relative;
            height: 26px;
            background: var(--bg);
            display: flex;
            align-items: center;
        }
        .perforated::before, .perforated::after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--bg);
        }
        .perforated::before { left: -13px; }
        .perforated::after  { right: -13px; }
        .perforation-line { flex: 1; border-top: 2px dashed rgba(212,175,55,0.2); margin: 0 20px; }

        /* Body */
        .ticket-body { padding: 2rem 2.5rem 2.5rem; }

        .order-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 2rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }
        .order-row:last-of-type { border-bottom: none; }

        .order-name { font-weight: 700; font-size: 0.95rem; line-height: 1.4; }
        .order-sub  { font-size: 0.78rem; color: var(--muted); margin-top: 0.2rem; }
        .order-amount { font-weight: 800; font-size: 1.05rem; color: var(--gold); white-space: nowrap; text-align: right; }
        .order-qty    { font-size: 0.75rem; color: var(--muted); text-align: right; margin-top: 0.2rem; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 2px solid rgba(212,175,55,0.25);
        }
        .total-label  { font-weight: 700; font-size: 1rem; }
        .total-amount { font-weight: 900; font-size: 1.5rem; color: var(--gold); }

        .ref-section {
            margin-top: 1.5rem;
            padding: 0.9rem 1.2rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px;
        }
        .ref-label { font-weight: 700; font-size: 0.78rem; display: block; margin-bottom: 0.2rem; }
        .ref-value { font-size: 0.72rem; color: var(--muted); word-break: break-all; }

        .ticket-footer {
            padding: 1.2rem 2.5rem;
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .footer-note  { font-size: 0.72rem; color: var(--muted); line-height: 1.5; }
        .footer-pulse { font-size: 0.78rem; font-weight: 900; color: var(--gold); opacity: 0.55; letter-spacing: 2px; text-transform: uppercase; }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; color: #111; padding: 0; }
            .action-bar { display: none !important; }
            .ticket { box-shadow: none; border: 1px solid #ddd; border-radius: 12px; }
            .ticket-header { background: #f8f3e2; }
            .perforated { background: #fff; }
            .perforated::before, .perforated::after { background: #fff; }
            .brand-name, .total-amount, .order-amount { color: #a07d10; }
            .status-badge { background: #d1fae5; color: #065f46; border-color: #6ee7b7; }
            .order-name, .total-label { color: #111; }
            .order-sub, .ref-value, .footer-note { color: #555; }
            .ticket-footer, .ref-section { background: #fafafa; }
            .brand-logo { filter: none; }
        }
    </style>
</head>
<body>

<div class="page-wrap">

    {{-- Action bar --}}
    <div class="action-bar">
        <a href="{{ route('dashboard') }}" class="btn-back">
            ← Volver al panel
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨&nbsp; Imprimir / Guardar PDF
        </button>
    </div>

    @php
        $orderList  = isset($orders) ? collect($orders) : collect([$order]);
        $grandTotal = $orderList->sum('amount');
        $firstOrder = $orderList->first();
        $stripeRef  = $firstOrder->stripe_session_id ?? null;
        $logoPath   = public_path('images/logoPulse.png');
        $logoB64    = file_exists($logoPath)
                        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                        : null;
    @endphp

    <div class="ticket">

        {{-- Header --}}
        <div class="ticket-header">
            <div class="brand">
                @if($logoB64)
                    <img src="{{ $logoB64 }}" alt="Pulse" class="brand-logo">
                @endif
                <div class="brand-text">
                    <div class="brand-name">Pulse</div>
                    <div class="brand-sub">Comprobante de compra</div>
                </div>
            </div>
            <div class="ticket-status">
                <div class="status-badge">
                    <div class="status-dot"></div>
                    Pagado
                </div>
                <div class="ticket-date">
                    {{ $firstOrder->updated_at->locale('es')->isoFormat('D MMM YYYY · HH:mm') }}
                </div>
            </div>
        </div>

        {{-- Perforation --}}
        <div class="perforated"><div class="perforation-line"></div></div>

        {{-- Body --}}
        <div class="ticket-body">

            @foreach($orderList as $o)
            <div class="order-row">
                <div>
                    <div class="order-name">{{ $o->item_name }}</div>
                    <div class="order-sub">
                        {{ $o->item_type === 'concert' ? 'Entrada de concierto' : 'Merchandising' }}
                        &nbsp;·&nbsp; Pedido #{{ $o->id }}
                    </div>
                </div>
                <div>
                    <div class="order-amount">{{ number_format($o->amount, 2, ',', '.') }} €</div>
                    <div class="order-qty">× {{ $o->quantity ?? 1 }} ud.</div>
                </div>
            </div>
            @endforeach

            <div class="total-row">
                <div class="total-label">Total pagado</div>
                <div class="total-amount">{{ number_format($grandTotal, 2, ',', '.') }} €</div>
            </div>

            @if($stripeRef)
            <div class="ref-section">
                <span class="ref-label">🔒 Referencia de pago (Stripe)</span>
                <span class="ref-value">{{ $stripeRef }}</span>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="ticket-footer">
            <div class="footer-note">
                Documento válido como justificante de compra.<br>
                Preséntalo en taquilla o guárdalo como referencia.
            </div>
            <div class="footer-pulse">Pulse</div>
        </div>

    </div>

</div>

</body>
</html>
