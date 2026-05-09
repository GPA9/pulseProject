<x-app-layout>
    <div class="container-fluid px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold mb-1" style="font-size:1.8rem;">Conciertos</h1>
                <p class="text-muted mb-0" style="font-size:0.9rem;">Encuentra los mejores conciertos de música independiente cerca de ti</p>
            </div>
            <span class="text-muted small">{{ $concerts->count() }} conciertos</span>
        </div>

        {{-- Main Layout --}}
        <form method="GET" action="{{ route('concerts.index') }}" id="concertsForm">

            {{-- Search Bar (igual que merch: ancho completo, encima del row) --}}
            <div class="position-relative mb-4">
                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3"
                    style="color: var(--pulse-text-secondary); pointer-events:none; left: 4px;"></i>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    class="merch-search-bar ps-5"
                    placeholder="Buscar por artista, sala o ciudad... (Intro para buscar)"
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <div class="row g-4">

                {{-- ── Sidebar ──────────────────────────────────────── --}}
                <div class="col-lg-3 col-xl-2">
                    <div class="merch-sidebar">

                        {{-- Sort --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-sort-down me-1"></i>Ordenar por</span>
                            <div class="d-flex flex-column gap-2">
                                @foreach(['date_asc' => 'Próximos primero', 'date_desc' => 'Más lejanos primero', 'price_asc' => 'Más baratos', 'price_desc' => 'Más caros', 'nearby' => 'Más cercanos'] as $val => $label)
                                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                        <input type="radio" name="sort" value="{{ $val }}" class="form-check-input m-0"
                                            style="accent-color: var(--pulse-primary);"
                                            {{ request('sort', 'date_asc') === $val ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Mi Ubicación --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-geo-alt-fill me-1"></i>Mi ubicación</span>
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="getUserLocation()">
                                <i class="bi bi-crosshair me-1"></i>Usar mi ubicación
                            </button>
                            <input type="hidden" name="lat" id="userLat" value="{{ request('lat') }}">
                            <input type="hidden" name="lng" id="userLng" value="{{ request('lng') }}">
                            <input type="hidden" name="radius" id="userRadius" value="{{ request('radius', 100) }}">
                            
                            @if(request('lat') && request('lng'))
                                <div class="mt-2 p-2 rounded" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);">
                                    <small class="text-success d-block">
                                        <i class="bi bi-check-circle me-1"></i>Ubicación activa
                                    </small>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <select class="form-select form-select-sm bg-dark text-light border-secondary" 
                                            name="radius" onchange="this.form.submit()">
                                            <option value="50" {{ request('radius') == 50 ? 'selected' : '' }}>50 km</option>
                                            <option value="100" {{ request('radius', 100) == 100 ? 'selected' : '' }}>100 km</option>
                                            <option value="200" {{ request('radius') == 200 ? 'selected' : '' }}>200 km</option>
                                            <option value="500" {{ request('radius') == 500 ? 'selected' : '' }}>500 km</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Ubicación: Comunidad → Provincia --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-geo-alt me-1"></i>Ubicación</span>

                            {{-- Toda España --}}
                            <label class="d-flex align-items-center gap-2 mb-2" style="cursor:pointer;">
                                <input type="radio" name="province" value="" class="form-check-input m-0"
                                    style="accent-color: var(--pulse-primary);"
                                    {{ !request('province') && !request('community') ? 'checked' : '' }}
                                    onchange="clearCommunity(); this.form.submit()">
                                <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">Toda España</span>
                            </label>
                            <input type="hidden" name="community" id="communityInput" value="{{ request('community') }}">

                            {{-- Acordeón Comunidades → Provincias --}}
                            @php
                                $selectedProvince  = request('province');
                                $selectedCommunity = request('community');
                                $expandCommunity   = $selectedCommunity;
                                if ($selectedProvince && !$expandCommunity) {
                                    foreach ($communities as $comm => $provs) {
                                        if (in_array($selectedProvince, $provs)) {
                                            $expandCommunity = $comm;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            <div class="province-accordion" style="max-height: 340px; overflow-y: auto; scrollbar-width: thin;">
                                @foreach($communities as $community => $provinces)
                                    @php $isOpen = ($expandCommunity === $community); @endphp
                                    <div class="province-item mb-1">
                                        <button type="button"
                                            class="province-toggle w-100 d-flex align-items-center justify-content-between {{ $isOpen ? 'open' : '' }}"
                                            onclick="toggleProvince(this)"
                                            data-community="{{ $community }}"
                                        >
                                            <span>{{ $community }}</span>
                                            <i class="bi bi-chevron-right province-chevron" style="font-size:0.7rem; transition: transform 0.2s;"></i>
                                        </button>

                                        <div class="province-cities {{ $isOpen ? '' : 'd-none' }}">
                                            {{-- Toda la comunidad --}}
                                            <label class="d-flex align-items-center gap-2 city-label" style="cursor:pointer;">
                                                <input type="radio" name="province" value="" class="form-check-input m-0"
                                                    style="accent-color: var(--pulse-primary); flex-shrink:0;"
                                                    {{ $selectedCommunity === $community && !$selectedProvince ? 'checked' : '' }}
                                                    onchange="setCommunity('{{ $community }}'); this.form.submit()">
                                                <span style="font-size:0.82rem; color: var(--pulse-primary); font-weight:600;">Toda {{ $community }}</span>
                                            </label>
                                            @foreach($provinces as $province)
                                                <label class="d-flex align-items-center gap-2 city-label" style="cursor:pointer;">
                                                    <input type="radio" name="province" value="{{ $province }}" class="form-check-input m-0"
                                                        style="accent-color: var(--pulse-primary); flex-shrink:0;"
                                                        {{ $selectedProvince === $province ? 'checked' : '' }}
                                                        onchange="setCommunity('{{ $community }}'); this.form.submit()">
                                                    <span style="font-size:0.82rem; color: var(--pulse-text-secondary);">{{ $province }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Género Musical --}}
                        @if($genres->isNotEmpty())
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-music-note-beamed me-1"></i>Género</span>
                            <div class="d-flex flex-column gap-2">
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="genre" value="" class="form-check-input m-0"
                                        style="accent-color: var(--pulse-primary);"
                                        {{ !request('genre') ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">Todos</span>
                                </label>
                                @foreach($genres as $genre)
                                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                        <input type="radio" name="genre" value="{{ $genre }}" class="form-check-input m-0"
                                            style="accent-color: var(--pulse-primary);"
                                            {{ request('genre') === $genre ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">{{ $genre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Limpiar filtros --}}
                        @if(request()->anyFilled(['search','sort','province','community','genre','max_price']))
                            <a href="{{ route('concerts.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-1" style="font-size:0.8rem;">
                                <i class="bi bi-x-circle me-1"></i>Limpiar filtros
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ── Concert Tickets Grid ────────────────────────── --}}
                <div class="col-lg-9 col-xl-10">

                    {{-- Active filters summary --}}
                    @if(request()->anyFilled(['search','province','community','genre']))
                        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                            <span class="text-muted small">Filtrando:</span>
                            @if(request('search'))
                                <span class="concert-filter-chip">
                                    <i class="bi bi-search me-1"></i>{{ request('search') }}
                                </span>
                            @endif
                            @if(request('community'))
                                <span class="concert-filter-chip">
                                    <i class="bi bi-map me-1"></i>{{ request('community') }}
                                </span>
                            @endif
                            @if(request('province'))
                                <span class="concert-filter-chip">
                                    <i class="bi bi-geo-alt me-1"></i>{{ request('province') }}
                                </span>
                            @endif
                            @if(request('genre'))
                                <span class="concert-filter-chip">
                                    <i class="bi bi-music-note me-1"></i>{{ request('genre') }}
                                </span>
                            @endif
                            <span class="text-muted small ms-auto">{{ $concerts->count() }} resultado(s)</span>
                        </div>
                    @endif

                    @if($concerts->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Sin conciertos</h4>
                            <p class="text-muted small">No hay conciertos próximos con estos filtros.</p>
                            <a href="{{ route('concerts.index') }}" class="btn btn-primary mt-2">Ver todos</a>
                        </div>
                    @else
                        <div class="concerts-grid">
                            @foreach($concerts as $concert)
                            @php
                                $daysLeft = now()->diffInDays($concert->date, false);
                                $isHot    = $daysLeft <= 7;
                                $isSoon   = $daysLeft <= 14;

                                // Color por género
                                $genreColors = [
                                    'Indie Pop'        => ['#6366f1','#8b5cf6'],
                                    'Rock Alternativo' => ['#ef4444','#dc2626'],
                                    'Jazz Fusión'      => ['#f59e0b','#d97706'],
                                    'Electrónica'      => ['#06b6d4','#0891b2'],
                                    'Folk'             => ['#10b981','#059669'],
                                    'Hip Hop'          => ['#8b5cf6','#7c3aed'],
                                    'Pop'              => ['#ec4899','#db2777'],
                                ];
                                $colors = $genreColors[$concert->genre] ?? ['#EFE1B5','#c8b97a'];
                                $gradFrom = $colors[0];
                                $gradTo   = $colors[1];
                            @endphp

                            <div class="concert-ticket" style="--grad-from: {{ $gradFrom }}; --grad-to: {{ $gradTo }};">

                                {{-- LEFT: Fecha (el "talón" de la entrada) --}}
                                <div class="ticket-stub">
                                    <div class="ticket-month">
                                        {{ strtoupper($concert->date->locale('es')->isoFormat('MMM')) }}
                                    </div>
                                    <div class="ticket-day">
                                        {{ $concert->date->format('d') }}
                                    </div>
                                    <div class="ticket-year">
                                        {{ $concert->date->format('Y') }}
                                    </div>
                                    <div class="ticket-divider-dots"></div>
                                    <div class="ticket-time">
                                        {{ $concert->date->format('H:i') }}
                                    </div>
                                    <div class="ticket-time-label">h</div>
                                </div>

                                {{-- Perforación decorativa --}}
                                <div class="ticket-perforation">
                                    <div class="perf-circle top"></div>
                                    <div class="perf-line"></div>
                                    <div class="perf-circle bottom"></div>
                                </div>

                                {{-- RIGHT: Contenido principal --}}
                                <div class="ticket-main">
                                    <div class="ticket-top">
                                        {{-- Badges --}}
                                        <div class="ticket-badges">
                                            @if($isHot)
                                                <span class="ticket-badge badge-hot">🔥 ¡Esta semana!</span>
                                            @elseif($isSoon)
                                                <span class="ticket-badge badge-soon">⚡ Próximo</span>
                                            @endif
                                            @if($concert->genre)
                                                <span class="ticket-badge badge-genre">{{ $concert->genre }}</span>
                                            @endif
                                        </div>

                                        {{-- Artista --}}
                                        <h3 class="ticket-artist">
                                            {{ $concert->musicianProfile->stage_name ?? 'Artista' }}
                                        </h3>

                                        {{-- Venue --}}
                                        <div class="ticket-venue">
                                            <i class="bi bi-building me-1"></i>{{ $concert->venue }}
                                        </div>

                                        {{-- Lugar --}}
                                        <div class="ticket-location">
                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                            {{ $concert->city }}
                                            @if($concert->province && $concert->province !== $concert->city)
                                                · {{ $concert->province }}
                                            @endif
                                            @if($concert->autonomous_community)
                                                <span class="ticket-community">· {{ $concert->autonomous_community }}</span>
                                            @endif
                                        </div>

                                        {{-- Dirección exacta + enlace Google Maps --}}
                                        @if($concert->address)
                                            @php
                                                $mapsUrl = $concert->latitude && $concert->longitude
                                                    ? 'https://www.google.com/maps?q=' . $concert->latitude . ',' . $concert->longitude
                                                    : 'https://www.google.com/maps/search/' . urlencode($concert->address);
                                            @endphp
                                            <div class="ticket-address">
                                                <i class="bi bi-signpost-2-fill me-1"></i>
                                                {{ $concert->address }}
                                                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="ticket-maps-link" title="Ver en Google Maps">
                                                    <i class="bi bi-map-fill me-1"></i>Ver en mapa
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Descripción --}}
                                        @if($concert->description)
                                            <p class="ticket-desc">{{ Str::limit($concert->description, 90) }}</p>
                                        @endif
                                    </div>

                                    <div class="ticket-bottom">
                                        {{-- Aforo --}}
                                        @if($concert->capacity)
                                            <div class="ticket-capacity">
                                                @if(($concert->capacity_available ?? $concert->capacity) <= 0)
                                                    <span class="badge" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);font-size:0.72rem;">
                                                        🚫 Agotado
                                                    </span>
                                                @else
                                                    <i class="bi bi-people me-1"></i>Entradas disponibles:
                                                    <strong style="color:var(--pulse-primary);">{{ number_format($concert->capacity_available ?? $concert->capacity, 0, ',', '.') }}</strong>
                                                    <span class="text-muted">/ {{ number_format($concert->capacity, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Precio y CTA --}}
                                        <div class="ticket-price-row">
                                            <div class="ticket-price-info">
                                                <span class="ticket-price-label">Precio desde</span>
                                                <span class="ticket-price">{{ number_format($concert->price, 2, ',', '.') }} €</span>
                                            </div>
                                            <a href="{{ $concert->ticketmaster_url }}" target="_blank" rel="noopener noreferrer"
                                                class="ticket-cta">
                                                <i class="bi bi-ticket-perforated me-1"></i>Comprar en Ticketmaster
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Decoración de fondo --}}
                                <div class="ticket-bg-decoration" aria-hidden="true">
                                    <i class="bi bi-music-note-beamed"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <style>
        /* ── Filter chips ─────────────────────────────────────────── */
        .concert-filter-chip {
            display: inline-flex;
            align-items: center;
            font-size: 0.78rem;
            color: var(--pulse-primary);
            background: rgba(239,225,181,0.1);
            border: 1px solid rgba(239,225,181,0.25);
            border-radius: 50px;
            padding: 2px 10px;
        }

        /* ── Concerts Grid ────────────────────────────────────────── */
        .concerts-grid {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        /* ── Ticket ───────────────────────────────────────────────── */
        .concert-ticket {
            display: flex;
            background: var(--pulse-card-bg);
            border: 1px solid var(--pulse-border);
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            min-height: 160px;
        }
        .concert-ticket::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(var(--grad-from-rgb, 99,102,241), 0.04) 0%,
                transparent 60%
            );
            pointer-events: none;
            border-radius: inherit;
        }
        .concert-ticket:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.35), 0 0 0 1px color-mix(in srgb, var(--grad-from) 40%, transparent);
            border-color: color-mix(in srgb, var(--grad-from) 50%, transparent);
        }

        /* ── Ticket Stub (date column) ───────────────────────────── */
        .ticket-stub {
            flex-shrink: 0;
            width: 90px;
            background: linear-gradient(160deg, var(--grad-from), var(--grad-to));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.5rem;
            text-align: center;
            position: relative;
        }
        .ticket-month {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
        }
        .ticket-day {
            font-size: 2.6rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            margin: 0.1rem 0;
        }
        .ticket-year {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
            font-weight: 600;
        }
        .ticket-divider-dots {
            width: 1px;
            height: 20px;
            background: repeating-linear-gradient(
                to bottom,
                rgba(255,255,255,0.4) 0px,
                rgba(255,255,255,0.4) 4px,
                transparent 4px,
                transparent 8px
            );
            margin: 0.5rem auto;
        }
        .ticket-time {
            font-size: 1.1rem;
            font-weight: 800;
            color: rgba(255,255,255,0.95);
        }
        .ticket-time-label {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.6);
        }

        /* ── Perforation ─────────────────────────────────────────── */
        .ticket-perforation {
            flex-shrink: 0;
            width: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 0;
            background: var(--pulse-card-bg);
            position: relative;
        }
        .perf-circle {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--pulse-bg);
            border: 1px solid var(--pulse-border);
            flex-shrink: 0;
            margin: -7px 0;
            position: relative;
            z-index: 1;
        }
        .perf-line {
            flex: 1;
            width: 1px;
            background: repeating-linear-gradient(
                to bottom,
                var(--pulse-border) 0px,
                var(--pulse-border) 6px,
                transparent 6px,
                transparent 12px
            );
        }

        /* ── Ticket Main ─────────────────────────────────────────── */
        .ticket-main {
            flex: 1;
            padding: 1.1rem 1.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }
        .ticket-top { flex: 1; }
        .ticket-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 0.5rem;
        }
        .ticket-badge {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 50px;
            letter-spacing: 0.03em;
        }
        .badge-hot {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge-soon {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .badge-genre {
            background: rgba(239,225,181,0.1);
            color: var(--pulse-primary);
            border: 1px solid rgba(239,225,181,0.2);
        }
        .ticket-artist {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--pulse-text);
            margin: 0 0 0.3rem;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ticket-venue {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--pulse-primary);
            margin-bottom: 0.25rem;
        }
        .ticket-location {
            font-size: 0.8rem;
            color: var(--pulse-text-secondary);
            margin-bottom: 0.4rem;
        }
        .ticket-community {
            opacity: 0.6;
        }
        .ticket-desc {
            font-size: 0.82rem;
            color: var(--pulse-text-secondary);
            margin: 0;
            line-height: 1.4;
        }
        .ticket-address {
            font-size: 0.78rem;
            color: var(--pulse-text-secondary);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .ticket-maps-link {
            display: inline-flex;
            align-items: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: #34d399;
            background: rgba(52, 211, 153, 0.1);
            border: 1px solid rgba(52, 211, 153, 0.25);
            border-radius: 50px;
            padding: 2px 8px;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.2s, color 0.2s;
        }
        .ticket-maps-link:hover {
            background: rgba(52, 211, 153, 0.2);
            color: #6ee7b7;
        }

        .ticket-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            border-top: 1px solid var(--pulse-border);
        }
        .ticket-capacity {
            font-size: 0.78rem;
            color: var(--pulse-text-secondary);
        }
        .ticket-price-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-left: auto;
        }
        .ticket-price-info {
            text-align: right;
        }
        .ticket-price-label {
            display: block;
            font-size: 0.65rem;
            color: var(--pulse-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .ticket-price {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--pulse-primary);
        }
        .ticket-cta {
            background: linear-gradient(135deg, var(--grad-from), var(--grad-to));
            border: none;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 0.55rem 1.2rem;
            border-radius: 50px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            white-space: nowrap;
        }
        .ticket-cta:hover {
            opacity: 0.88;
            transform: scale(1.03);
        }

        /* ── Background decoration ───────────────────────────────── */
        .ticket-bg-decoration {
            position: absolute;
            right: -10px;
            top: 50%;
            transform: translateY(-50%) rotate(15deg);
            font-size: 8rem;
            color: rgba(255,255,255,0.025);
            pointer-events: none;
            user-select: none;
        }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 576px) {
            .ticket-stub { width: 72px; }
            .ticket-day  { font-size: 2rem; }
            .ticket-main { padding: 0.8rem 1rem; }
            .ticket-artist { font-size: 1.05rem; }
            .ticket-price  { font-size: 1.1rem; }
        }

        /* ── Province accordion (igual que merch) ─────────────────── */
        .province-toggle {
            background: none;
            border: none;
            border-radius: 6px;
            padding: 5px 8px;
            color: var(--pulse-text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-align: left;
            transition: background 0.15s, color 0.15s;
        }
        .province-toggle:hover,
        .province-toggle.open {
            background: rgba(239,225,181,0.08);
            color: var(--pulse-primary);
        }
        .province-toggle.open .province-chevron {
            transform: rotate(90deg);
        }
        .province-cities {
            padding: 4px 0 4px 16px;
        }
        .city-label {
            padding: 3px 0;
        }
        .province-accordion::-webkit-scrollbar {
            width: 4px;
        }
        .province-accordion::-webkit-scrollbar-track {
            background: transparent;
        }
        .province-accordion::-webkit-scrollbar-thumb {
            background: var(--pulse-border);
            border-radius: 4px;
        }
    </style>

    <script>
        // ── Search: only submit on Enter ──────────────────────────────────
        document.getElementById('searchInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('concertsForm').submit();
            }
        });

        // ── Community accordion toggle ────────────────────────────────
        function toggleProvince(btn) {
            const list   = btn.nextElementSibling;
            const isOpen = !list.classList.contains('d-none');
            document.querySelectorAll('.province-cities').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.province-toggle').forEach(el => el.classList.remove('open'));
            if (!isOpen) {
                list.classList.remove('d-none');
                btn.classList.add('open');
            }
        }

        function setCommunity(comm) {
            document.getElementById('communityInput').value = comm;
        }

        function clearCommunity() {
            document.getElementById('communityInput').value = '';
        }

        // ── Geolocation for nearby concerts ───────────────────────────
        function getUserLocation() {
            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('userLat').value = position.coords.latitude;
                    document.getElementById('userLng').value = position.coords.longitude;
                    document.getElementById('concertsForm').submit();
                },
                (error) => {
                    let message = 'Error al obtener ubicación';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Permiso de ubicación denegado';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Ubicación no disponible';
                            break;
                        case error.TIMEOUT:
                            message = 'Tiempo de espera agotado';
                            break;
                    }
                    alert(message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }

        // ── Leaflet Map for nearby concerts ─────────────────────────────
        let map = null;
        let markers = [];

        function initMap() {
            const latInput = document.getElementById('userLat');
            const lngInput = document.getElementById('userLng');
            
            if (!latInput.value || !lngInput.value) return;

            // Create map container
            const mapContainer = document.createElement('div');
            mapContainer.id = 'concertsMap';
            mapContainer.style.height = '300px';
            mapContainer.style.borderRadius = '12px';
            mapContainer.style.marginTop = '1rem';
            
            // Insert map after the header
            document.querySelector('.container-fluid > .d-flex').after(mapContainer);

            // Initialize Leaflet
            map = L.map('concertsMap').setView([latInput.value, lngInput.value], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Add user marker
            const userIcon = L.divIcon({
                className: 'user-marker',
                html: '<div style="width:16px;height:16px;background:#3b82f6;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            L.marker([latInput.value, lngInput.value], { icon: userIcon })
                .addTo(map)
                .bindPopup('<strong>Tu ubicación</strong>');

            // Load concert markers
            loadConcertMarkers();
        }

        function loadConcertMarkers() {
            const lat = document.getElementById('userLat').value;
            const lng = document.getElementById('userLng').value;
            const radius = document.getElementById('userRadius')?.value || 100;

            fetch(`/conciertos/map-data?lat=${lat}&lng=${lng}&radius=${radius}`)
                .then(res => res.json())
                .then(data => {
                    data.concerts.forEach(concert => {
                        const icon = L.divIcon({
                            className: 'concert-marker',
                            html: `<div style="width:32px;height:32px;background:var(--pulse-primary);border:2px solid white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.3);cursor:pointer;"><i class="bi bi-music-note" style="color:white;font-size:14px;"></i></div>`,
                            iconSize: [32, 32],
                            iconAnchor: [16, 32]
                        });

                        const marker = L.marker([concert.lat, concert.lng], { icon: icon })
                            .addTo(map)
                            .bindPopup(`
                                <div style="min-width:150px;">
                                    <strong>${concert.artist}</strong><br>
                                    <small>${concert.venue}</small><br>
                                    <small class="text-muted">${concert.city}</small><br>
                                    <small>${concert.date}</small><br>
                                    <strong style="color:var(--pulse-primary);">${concert.price.toFixed(2)} €</strong>
                                    ${concert.distance ? `<br><small class="text-info">📍 ${Math.round(concert.distance)} km</small>` : ''}
                                </div>
                            `);
                        markers.push(marker);
                    });

                    // Fit bounds if there are markers
                    if (markers.length > 0) {
                        const group = L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                });
        }

        // Initialize map on page load if coordinates exist
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('userLat').value && document.getElementById('userLng').value) {
                // Load Leaflet dynamically
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);

                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = initMap;
                document.head.appendChild(script);
            }
        });
    </script>
</x-app-layout>
