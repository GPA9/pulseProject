<x-app-layout>
    <div class="container-fluid py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1"><i class="bi bi-people-fill me-2" style="color:var(--pulse-primary);"></i>Músicos</h2>
                <p class="text-white-50 mb-0">Gestión de perfiles de artistas</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
        </div>

        @if(session('success'))
            <div class="alert border-0 mb-4" style="background:rgba(16,185,129,0.15);color:#34d399;border-radius:12px;">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- Filtros --}}
        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-white-50 small">Buscar</label>
                        <input type="text" name="search" class="form-control bg-black border-secondary text-light" 
                               value="{{ request('search') }}" placeholder="Nombre, género, email...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50 small">Suscripción</label>
                        <select name="subscription_status" class="form-select bg-black border-secondary text-light">
                            <option value="">Todos</option>
                            <option value="active" {{ request('subscription_status') === 'active' ? 'selected' : '' }}>Con plan activo</option>
                            <option value="none" {{ request('subscription_status') === 'none' ? 'selected' : '' }}>Sin plan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50 small">Comunidad</label>
                        <select name="community" class="form-select bg-black border-secondary text-light">
                            <option value="">Todas</option>
                            @foreach($communities as $c)
                                <option value="{{ $c }}" {{ request('community') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla de músicos --}}
        <div class="card border-0" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-borderless align-middle mb-0">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <th class="text-white-50 fw-bold ps-4" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Artista</th>
                                <th class="text-white-50 fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Género</th>
                                <th class="text-white-50 fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Ubicación</th>
                                <th class="text-white-50 fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Plan</th>
                                <th class="text-white-50 fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Precio</th>
                                <th class="text-white-50 fw-bold text-end pe-4" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($musicians as $m)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($m->image_path)
                                            <img src="{{ asset('images/band-logos/' . $m->image_path) }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" alt="">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="bi bi-person-fill text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-white">{{ $m->stage_name }}</div>
                                            <small class="text-white-50">{{ $m->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-white">{{ $m->genre }}</td>
                                <td class="text-white-50">{{ $m->city }}, {{ $m->autonomous_community }}</td>
                                <td>
                                    @php $activeSub = $m->subscriptions()->active()->first(); @endphp
                                    @if($activeSub)
                                        <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);">{{ ucfirst($activeSub->plan_type) }}</span>
                                    @else
                                        <span class="badge" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);">Sin plan</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold" style="color:var(--pulse-primary);">
                                    @if($activeSub)
                                        {{ number_format($activeSub->price, 2, ',', '.') }} €/mes
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.musicians.detail', $m->id) }}" class="btn btn-outline-primary btn-sm" title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.musicians.destroy', $m->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este perfil de músico?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">{{ $musicians->withQueryString()->links() }}</div>
    </div>
</x-app-layout>
