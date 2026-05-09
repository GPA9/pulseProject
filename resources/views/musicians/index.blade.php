<x-app-layout>
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Descubre Artistas</h2>
                <p class="text-muted lead">Encuentra el mejor talento local en tu ciudad.</p>
            </div>
        </div>

        {{-- ═══ Barra de Filtros ═══ --}}
        <form method="GET" action="{{ route('musicians.index') }}" id="filterForm">
            <div class="row gx-3 gy-2 justify-content-center mb-5">
                {{-- Barra de Búsqueda --}}
                <div class="col-12 col-lg-6 mb-3">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y ms-3"
                            style="color: var(--pulse-text-secondary); pointer-events:none; left: 4px;"></i>
                        <input type="text" name="search" id="searchInput" class="form-control ps-5 bg-dark text-light border-secondary"
                            placeholder="Buscar músicos por nombre..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-12 col-md-5 col-lg-3">
                    <select name="community" id="filterCommunity"
                        class="form-select bg-dark text-light border-secondary"
                        onchange="updateProvinces(this.value, 'filterProvince', null); this.form.submit();">
                        <option value="">— Todas las Comunidades —</option>
                        @foreach($communities as $community => $provinces)
                            <option value="{{ $community }}" {{ request('community') === $community ? 'selected' : '' }}>
                                {{ $community }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 col-lg-3">
                    <select name="province" id="filterProvince" class="form-select bg-dark text-light border-secondary"
                        onchange="this.form.submit()">
                        <option value="">— Todas las Provincias —</option>
                        @if(request('community') && isset($communities[request('community')]))
                            @foreach($communities[request('community')] as $province)
                                <option value="{{ $province }}" {{ request('province') === $province ? 'selected' : '' }}>
                                    {{ $province }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                {{-- Botón de Mi Ubicación --}}
                <div class="col-12 col-md-2 col-lg-2">
                    @include('partials.location-button')
                </div>
                @if(request('community') || request('province') || request('search'))
                    <div class="col-auto">
                        <a href="{{ route('musicians.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    </div>
                @endif
            </div>
        </form>

        {{-- ═══ Grid de Músicos ═══ --}}
        @if($musicians->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-music-note-beamed display-3 mb-3 d-block opacity-50"></i>
                <p class="lead">No hay artistas en esta zona todavía.</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($musicians as $musician)
                    <div class="col">
                        <a href="{{ route('musicians.show', $musician) }}" class="text-decoration-none">
                            <div class="card h-100 bg-transparent border-0 text-center hover-scale">
                                <div class="card-body p-2">
                                    <div class="mb-3 position-relative">
                                        @if($musician->image_path)
                                            @if(file_exists(public_path('images/band-logos/' . $musician->image_path)))
                                                <img src="{{ asset('images/band-logos/' . $musician->image_path) }}"
                                            @else
                                                <img src="{{ asset('storage/' . $musician->image_path) }}"
                                            @endif
                                                class="rounded-circle shadow-lg" alt="{{ $musician->stage_name }}"
                                                style="width: 180px; height: 180px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-dark border border-secondary d-flex align-items-center justify-content-center mx-auto shadow-lg"
                                                style="width: 180px; height: 180px;">
                                                <i class="bi bi-person-fill fs-1 text-muted"></i>
                                            </div>
                                        @endif
                                        <div
                                            class="position-absolute bottom-0 end-0 me-4 mb-2 bg-primary rounded-circle p-2 shadow d-none d-md-block">
                                            <i class="bi bi-play-fill text-black fs-5"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold mb-1 text-white">{{ $musician->stage_name }}</h5>
                                    <p class="card-text small text-muted mb-1">{{ $musician->genre }}</p>
                                    @if($musician->autonomous_community)
                                        <span class="badge bg-primary bg-opacity-20 text-primary mt-1 me-1"
                                            style="font-size:0.7rem;">
                                            {{ $musician->autonomous_community }}
                                        </span>
                                    @endif
                                    @if($musician->province)
                                        <span class="badge bg-secondary bg-opacity-25 text-light mt-1" style="font-size:0.7rem;">
                                            {{ $musician->province }}
                                        </span>
                                    @elseif($musician->city)
                                        <span class="badge bg-secondary bg-opacity-25 text-light mt-1" style="font-size:0.7rem;">
                                            {{ $musician->city }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        // Mapa de comunidades y provincias (desde PHP)
        const communitiesMap = @json($communities);

        function updateProvinces(community, provinceSelectId, selectedProvince) {
            const sel = document.getElementById(provinceSelectId);
            sel.innerHTML = '<option value="">— Todas las Provincias —</option>';

            if (community && communitiesMap[community]) {
                communitiesMap[community].forEach(function (prov) {
                    const opt = document.createElement('option');
                    opt.value = prov;
                    opt.textContent = prov;
                    if (selectedProvince && prov === selectedProvince) opt.selected = true;
                    sel.appendChild(opt);
                });
                sel.disabled = false;
            } else {
                sel.disabled = false;
            }
        }

        // On page load, populate province dropdown if community is already selected
        document.addEventListener('DOMContentLoaded', function () {
            const community = document.getElementById('filterCommunity').value;
            const currentProvince = "{{ request('province') }}";
            if (community) {
                updateProvinces(community, 'filterProvince', currentProvince);
                // Re-select the province after re-populating
                if (currentProvince) {
                    document.getElementById('filterProvince').value = currentProvince;
                }
            }
        });
    </script>
</x-app-layout>