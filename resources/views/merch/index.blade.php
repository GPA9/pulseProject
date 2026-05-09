<x-app-layout>
    <div class="container-fluid px-4 py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold mb-1" style="font-size:1.8rem;">Merchandising</h1>
                <p class="text-muted mb-0" style="font-size:0.9rem;">Apoya a tus artistas locales favoritos</p>
            </div>
            <span class="text-muted small">{{ $merches->count() }} productos</span>
        </div>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('merch.index') }}" id="filterForm">
            <div class="position-relative mb-4">
                <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3"
                    style="color: var(--pulse-text-secondary); pointer-events:none; left: 4px;"></i>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    class="merch-search-bar ps-5"
                    placeholder="Buscar por grupo, artista o producto... (Intro para buscar)"
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <div class="row g-4">
                {{-- Sidebar Filters --}}
                <div class="col-lg-3 col-xl-2">
                    <div class="merch-sidebar">

                        {{-- Sort --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-sort-down me-1"></i>Ordenar por</span>
                            <div class="d-flex flex-column gap-2">
                                @foreach(['' => 'Todos', 'newest' => 'Más nuevos', 'bestsellers' => 'Más vendidos', 'cheapest' => 'Más baratos', 'expensive' => 'Más caros'] as $val => $label)
                                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                        <input type="radio" name="sort" value="{{ $val }}" class="form-check-input m-0"
                                            style="accent-color: var(--pulse-primary);"
                                            {{ request('sort', '') === $val ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Comunidad Autónoma → Provincia --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-geo-alt me-1"></i>Ubicación</span>

                            {{-- Botón de Mi Ubicación --}}
                            <div class="mb-3">
                                @include('partials.location-button')
                            </div>

                            {{-- Toda España --}}
                            <label class="d-flex align-items-center gap-2 mb-2" style="cursor:pointer;">
                                <input type="radio" name="community" value="" class="form-check-input m-0"
                                    @if(!request()->filled('community')) checked @endif
                                    onchange="this.form.submit()">
                                <span class="small">🇪🇸 Toda España</span>
                            </label>

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

                        {{-- Category --}}
                        <div class="filter-section">
                            <span class="filter-label"><i class="bi bi-tag me-1"></i>Categoría</span>
                            <div class="d-flex flex-column gap-2">
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="category" value="" class="form-check-input m-0"
                                        style="accent-color: var(--pulse-primary);"
                                        {{ !request('category') ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">Todas</span>
                                </label>
                                @foreach($categories as $cat)
                                    <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                        <input type="radio" name="category" value="{{ $cat }}" class="form-check-input m-0"
                                            style="accent-color: var(--pulse-primary);"
                                            {{ request('category') === $cat ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span style="font-size:0.88rem; color: var(--pulse-text-secondary);">{{ $cat }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Reset --}}
                        @if(request()->anyFilled(['search','sort','province','community','category']))
                            <a href="{{ route('merch.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-1" style="font-size:0.8rem;">
                                <i class="bi bi-x-circle me-1"></i>Limpiar filtros
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="col-lg-9 col-xl-10">
                    @if($merches->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-bag-x display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Sin resultados</h4>
                            <p class="text-muted small">Intenta con otros filtros o términos de búsqueda.</p>
                            <a href="{{ route('merch.index') }}" class="btn btn-primary mt-2">Ver todo</a>
                        </div>
                    @else
                        <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-4">
                            @foreach($merches as $item)
                                <div class="col">
                                    <div class="merch-card h-100">
                                        <div class="merch-img-wrap">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                                            @else
                                                @php
                                                    $gradients = [
                                                        'Camisetas' => 'linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%)',
                                                        'Sudaderas' => 'linear-gradient(135deg, #1a0a0a 0%, #2d1515 50%, #4a1515 100%)',
                                                        'Gorras'    => 'linear-gradient(135deg, #0a1a0a 0%, #152d15 50%, #1a4a1a 100%)',
                                                        'Pósters'   => 'linear-gradient(135deg, #1a1500 0%, #2d2500 50%, #4a3d00 100%)',
                                                        'Bolsas'    => 'linear-gradient(135deg, #10101a 0%, #1a1a2d 50%, #252545 100%)',
                                                    ];
                                                    $icons = [
                                                        'Camisetas' => 'bi-person-fill',
                                                        'Sudaderas' => 'bi-person-fill',
                                                        'Gorras'    => 'bi-person-bounding-box',
                                                        'Pósters'   => 'bi-image',
                                                        'Bolsas'    => 'bi-bag-fill',
                                                    ];
                                                    $grad = $gradients[$item->category] ?? 'linear-gradient(135deg, #1a1a1a, #333)';
                                                    $icon = $icons[$item->category] ?? 'bi-box';
                                                @endphp
                                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:{{ $grad }};">
                                                    <i class="bi {{ $icon }}" style="font-size:3rem;color:rgba(239,225,181,0.25);"></i>
                                                </div>
                                            @endif
                                            @if($item->sales_count > 200)
                                                <span class="merch-badge">🔥 Top</span>
                                            @elseif($item->created_at->gt(now()->subDays(7)))
                                                <span class="merch-badge">Nuevo</span>
                                            @endif
                                        </div>
                                        <div class="merch-body">
                                            <span class="merch-artist">{{ $item->musicianProfile->stage_name ?? 'Artista' }}</span>
                                            <span class="merch-title">{{ $item->name }}</span>
                                            <span class="merch-city">
                                                <i class="bi bi-geo-alt-fill me-1" style="font-size:0.7rem;"></i>
                                                {{ $item->city ?? $item->musicianProfile->city }}
                                            </span>
                                            <div class="merch-footer">
                                                <span class="merch-price">{{ number_format($item->price, 2, ',', '.') }} €</span>
                                                <a href="{{ $item->merchbar_url }}" target="_blank" rel="noopener noreferrer"
                                                    class="btn btn-primary btn-sm"
                                                    style="border-radius: 50px; font-size:0.78rem; padding: 4px 14px;">
                                                    <i class="bi bi-bag-plus me-1"></i>Comprar en Merchbar
                                                </a>
                                            </div>
                                        </div>
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
        // ── Search: only submit on Enter ──────────────────────────────
        document.getElementById('searchInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
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
    </script>
</x-app-layout>