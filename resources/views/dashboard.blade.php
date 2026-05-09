<x-app-layout>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">Panel de Control</h2>
                <p class="text-muted">Bienvenido de nuevo, {{ Auth::user()->name }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success bg-success text-white border-0 mb-4">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 mb-4">
                <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>No se pudo completar la acción:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                @if(Auth::user()->role === 'user')

                    {{-- ── Historial de Compras ───────────────────────────── --}}
                    @php
                        $concertOrders = $orders->where('item_type', 'concert')->where('status', 'paid');
                        $merchOrders   = $orders->where('item_type', 'merch')->where('status', 'paid');
                        $pendingOrders = $orders->where('status', 'pending');
                        // Exclude stale pending orders (>30 min) — abandoned checkouts
                        $visibleOrders = $orders->filter(function($o) {
                            if ($o->status === 'pending') {
                                return $o->created_at->diffInMinutes(now()) < 30;
                            }
                            return true;
                        });
                    @endphp

                    {{-- Resumen tarjetas --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--pulse-surface);border:1px solid var(--pulse-border);">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:48px;height:48px;background:rgba(239,225,181,0.1);">
                                    <i class="bi bi-bag-check" style="font-size:1.4rem;color:var(--pulse-primary);"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;">Total pagado</div>
                                    <div class="fw-bold fs-5" style="color:var(--pulse-primary);">{{ number_format($orders->where('status','paid')->sum('amount'), 2, ',', '.') }} €</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--pulse-surface);border:1px solid var(--pulse-border);">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:48px;height:48px;background:rgba(16,185,129,0.1);">
                                    <i class="bi bi-ticket-perforated" style="font-size:1.4rem;color:#10b981;"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;">Entradas</div>
                                    <div class="fw-bold fs-5 text-white">{{ $concertOrders->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--pulse-surface);border:1px solid var(--pulse-border);">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:48px;height:48px;background:rgba(245,158,11,0.1);">
                                    <i class="bi bi-bag" style="font-size:1.4rem;color:#f59e0b;"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;">Merch comprado</div>
                                    <div class="fw-bold fs-5 text-white">{{ $merchOrders->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mis entradas de concierto --}}
                    @if($concertOrders->isNotEmpty())
                    <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
                        <div style="height:4px;background:linear-gradient(90deg,#10b981,#059669);border-radius:16px 16px 0 0;"></div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2" style="color:#10b981;"></i>Mis Entradas</h5>
                            <div class="d-flex flex-column gap-2">
                                @foreach($concertOrders->sortByDesc('created_at') as $ord)
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03);border:1px solid var(--pulse-border);">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:44px;height:44px;background:rgba(16,185,129,0.12);">
                                        <i class="bi bi-calendar-event" style="color:#10b981;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold text-white" style="font-size:0.9rem;">{{ $ord->item_name }}</div>
                                        <small class="text-muted">{{ $ord->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-bold" style="color:var(--pulse-primary);">{{ number_format($ord->amount, 2, ',', '.') }} €</div>
                                        <span class="badge bg-success" style="font-size:0.7rem;">Pagado</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Mis compras de merch --}}
                    @if($merchOrders->isNotEmpty())
                    <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
                        <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#d97706);border-radius:16px 16px 0 0;"></div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3"><i class="bi bi-bag me-2" style="color:#f59e0b;"></i>Mis Compras Merch</h5>
                            <div class="d-flex flex-column gap-2">
                                @foreach($merchOrders->sortByDesc('created_at') as $ord)
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,255,255,0.03);border:1px solid var(--pulse-border);">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0" style="width:44px;height:44px;background:rgba(245,158,11,0.12);">
                                        <i class="bi bi-box" style="color:#f59e0b;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold text-white" style="font-size:0.9rem;">{{ $ord->item_name }}</div>
                                        <small class="text-muted">{{ $ord->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-bold" style="color:var(--pulse-primary);">{{ number_format($ord->amount, 2, ',', '.') }} €</div>
                                        <span class="badge bg-success" style="font-size:0.7rem;">Pagado</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                @else

                    {{-- ── PERFIL DE MÚSICO ───────────────────────────── --}}
                    @php
                        $profile = Auth::user()->musicianProfile;
                    @endphp

                    @if($profile)
                    <div class="row g-4 mb-4">
                        <div class="col-lg-4">
                            <div class="card border-0 h-100" style="background:var(--pulse-surface);border-radius:16px;">
                                <div class="card-body p-4 text-center">
                                    <div class="position-relative d-inline-block mb-3" style="cursor:pointer;" 
                                         data-bs-toggle="modal" data-bs-target="#editProfileImageModal" title="Cambiar foto de perfil">
                                        @if($profile->image_path)
                                            <img src="{{ asset('images/band-logos/' . $profile->image_path) }}" 
                                                 class="rounded-circle" style="width:120px;height:120px;object-fit:cover;" alt="">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                 style="width:120px;height:120px;">
                                                <i class="bi bi-person-fill fs-1 text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle d-flex align-items-center justify-content-center"
                                             style="background:rgba(0,0,0,0.55);opacity:0;transition:opacity 0.3s;"
                                             onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                            <i class="bi bi-camera-fill text-white" style="font-size:1.5rem;"></i>
                                            <span class="text-white ms-2 fw-semibold" style="font-size:0.85rem;">Cambiar</span>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold mb-1">{{ $profile->stage_name }}</h4>
                                    <p class="text-muted mb-3">{{ $profile->genre }}</p>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <span class="badge bg-primary bg-opacity-15 text-primary">{{ $profile->city }}</span>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                        <i class="bi bi-pencil me-1"></i>Editar Perfil
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-8">
                            <div class="card border-0 h-100" style="background:var(--pulse-surface);border-radius:16px;">
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <h5 class="fw-bold mb-1">Estadísticas Rápidas</h5>
                                        <p class="text-muted mb-0" style="font-size:0.9rem;">Resumen de tu actividad musical y acceso directo a nuevas publicaciones.</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(239,225,181,0.08);border:1px solid rgba(239,225,181,0.2);">
                                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:48px;height:48px;background:rgba(239,225,181,0.1);">
                                                    <i class="bi bi-music-note-list" style="font-size:1.2rem;color:var(--pulse-primary);"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Canciones</div>
                                                    <div class="fw-bold fs-4" style="color:var(--pulse-primary);">{{ $profile->songs->count() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
                                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width:48px;height:48px;background:rgba(16,185,129,0.1);">
                                                    <i class="bi bi-play-circle" style="font-size:1.2rem;color:#10b981;"></i>
                                                </div>
                                                <div>
                                                    <div class="text-muted small">Reproducciones</div>
                                                    <div class="fw-bold fs-4" style="color:#10b981;">{{ number_format($profile->songs->sum('play_count')) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            @php
                                                $hasActiveSubscription = $profile->subscriptions && $profile->subscriptions()->active()->exists();
                                            @endphp
                                            @if($hasActiveSubscription)
                                            <div class="d-flex align-items-center justify-content-between gap-3 p-3 rounded-3" style="background:linear-gradient(135deg, rgba(239,225,181,0.08), rgba(201,168,76,0.12));border:1px solid rgba(239,225,181,0.22);">
                                                <div>
                                                    <div class="fw-semibold text-white mb-1">
                                                        <i class="bi bi-cloud-upload me-2" style="color:var(--pulse-primary);"></i>Sube nueva música
                                                    </div>
                                                    <div class="text-muted" style="font-size:0.85rem;">Publica una pista o un álbum para mantener tu perfil activo.</div>
                                                </div>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMusicModal">
                                                    <i class="bi bi-plus-circle me-1"></i>Subir ahora
                                                </button>
                                            </div>
                                            @else
                                            <div class="d-flex align-items-center justify-content-between gap-3 p-3 rounded-3" style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.2);">
                                                <div>
                                                    <div class="fw-semibold text-white mb-1">
                                                        <i class="bi bi-lock me-2" style="color:#f87171;"></i>Sube nueva música
                                                    </div>
                                                    <div class="text-muted" style="font-size:0.85rem;">Necesitas un plan de almacenamiento para subir música.</div>
                                                </div>
                                                <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-danger">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>Ver Planes
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── ÚLTIMAS CANCIONES ───────────────────────────── --}}
                    @if($profile->songs->isNotEmpty())
                        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="fw-bold mb-0"><i class="bi bi-music-note-beamed me-2" style="color:var(--pulse-primary);"></i>Últimas Canciones</h5>
                                    <a href="#" class="btn btn-outline-primary btn-sm">Ver todas</a>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($profile->songs()->latest()->take(5)->get() as $idx => $song)
                                        <div class="d-flex align-items-center gap-3 p-2 rounded"
                                            style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
                                            <span class="text-muted"
                                                style="width:20px; font-size:0.85rem;">{{ $idx + 1 }}</span>
                                            <div class="d-flex align-items-center justify-content-center rounded bg-dark border border-secondary flex-shrink-0"
                                                style="width:36px;height:36px;">
                                                @if($song->cover_path)
                                                    <img src="{{ asset('storage/' . $song->cover_path) }}"
                                                        class="w-100 h-100 rounded" style="object-fit:cover;" alt="">
                                                @else
                                                    <i class="bi bi-music-note text-muted" style="font-size:0.85rem;"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="fw-semibold text-white" style="font-size:0.9rem;">
                                                    {{ $song->title }}
                                                </div>
                                                @if($song->album)
                                                    <div class="text-muted" style="font-size:0.75rem;"><i
                                                            class="bi bi-collection me-1"></i>{{ $song->album->title }}</div>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <small class="text-muted text-nowrap">{{ number_format($song->play_count) }} reprod.</small>
                                                <form method="POST" action="{{ route('songs.destroy', $song) }}" onsubmit="return confirm('¿Eliminar esta canción? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar canción">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── GESTIÓN DE MERCHANDISING ───────────────────── --}}
                    <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
                        <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#d97706);border-radius:16px 16px 0 0;"></div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold mb-0"><i class="bi bi-bag-plus me-2" style="color:#f59e0b;"></i>Gestión de Merchandising</h5>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addMerchModal">
                                    <i class="bi bi-plus-circle me-1"></i>Añadir Producto
                                </button>
                            </div>

                            @if($profile && $profile->merch->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-dark table-borderless align-middle mb-0">
                                        <thead>
                                            <tr style="border-bottom:1px solid var(--pulse-border);">
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Producto</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Categoría</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Ciudad</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Precio</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Tallas</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Vendidos</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($profile->merch->sortBy('created_at') as $merch)
                                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                                <td>
                                                    <div class="fw-semibold text-white">{{ $merch->name }}</div>
                                                </td>
                                                <td><span class="badge" style="background:rgba(245,158,11,0.12);color:#f59e0b;">{{ $merch->category }}</span></td>
                                                <td class="text-muted">{{ $merch->city ?? '—' }}</td>
                                                <td class="text-end fw-bold text-white">{{ number_format($merch->price, 2, ',', '.') }} €</td>
                                                <td class="text-muted">
                                                    @if(!empty($merch->sizes) && is_array($merch->sizes))
                                                        {{ implode(', ', $merch->sizes) }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold" style="color:#10b981;">{{ $merch->sales_count ?? 0 }}</td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('merch.edit', $merch->id) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('merch.destroy', $merch->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-bag-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No hay productos de merchandising</h5>
                                    <p class="text-muted">Añade tu primer producto para empezar a vender</p>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addMerchModal">
                                        <i class="bi bi-plus-circle me-1"></i>Añadir Producto
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── GESTIÓN DE CONCIERTOS ───────────────────── --}}
                    <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
                        <div style="height:4px;background:linear-gradient(90deg,#10b981,#059669);border-radius:16px 16px 0 0;"></div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold mb-0"><i class="bi bi-calendar-plus me-2" style="color:#10b981;"></i>Gestión de Conciertos</h5>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addConcertModal">
                                    <i class="bi bi-plus-circle me-1"></i>Añadir Concierto
                                </button>
                            </div>

                            @if($profile && $profile->concerts->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-dark table-borderless align-middle mb-0">
                                        <thead>
                                            <tr style="border-bottom:1px solid var(--pulse-border);">
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Fecha</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Venue</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Dirección</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Ciudad</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Provincia</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Comunidad</th>
                                                <th class="text-muted fw-bold" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Género</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Precio</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Aforo</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Disponibles</th>
                                                <th class="text-muted fw-bold text-end" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($profile->concerts->sortBy('date') as $concert)
                                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                                <td>
                                                    <div class="text-white">
                                                        <div class="fw-semibold">{{ $concert->date->format('d/m/Y') }}</div>
                                                        <small class="text-muted">{{ $concert->date->format('H:i') }}h</small>
                                                    </div>
                                                </td>
                                                <td class="fw-semibold text-white">
                                                    @if($concert->ticketmaster_url)
                                                        <a href="{{ $concert->ticketmaster_url }}" target="_blank" class="text-white text-decoration-none">
                                                            {{ $concert->venue }}
                                                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                        </a>
                                                    @else
                                                        {{ $concert->venue }}
                                                    @endif
                                                </td>
                                                <td class="text-muted">{{ $concert->address ?? '—' }}</td>
                                                <td class="text-muted">{{ $concert->city }}</td>
                                                <td class="text-muted">{{ $concert->province ?? '—' }}</td>
                                                <td class="text-muted">{{ $concert->autonomous_community ?? '—' }}</td>
                                                <td class="text-muted">{{ $concert->genre ?? '—' }}</td>
                                                <td class="text-end fw-bold text-white">{{ number_format($concert->price, 2, ',', '.') }} €</td>
                                                <td class="text-end">
                                                    @if($concert->capacity)
                                                        <span class="badge bg-info">{{ $concert->capacity }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($concert->capacity_available !== null)
                                                        @if($concert->capacity_available <= 0)
                                                            <span class="badge bg-danger">Agotado</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $concert->capacity_available }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('concerts.edit', $concert->id) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('concerts.destroy', $concert->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este concierto?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">No hay conciertos programados</h5>
                                    <p class="text-muted">Añade tu primer concierto para empezar a vender entradas</p>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addConcertModal">
                                        <i class="bi bi-plus-circle me-1"></i>Añadir Concierto
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── SUSCRIPCIÓN DE ALMACENAMIENTO ───────────────────── --}}
                    @php
                        $activeSubscription = ($profile && $profile->subscriptions)
                            ? $profile->subscriptions()->active()->first()
                            : null;
                    @endphp
                    @if($activeSubscription)
                        <div class="col-12 mt-2">
                            <div class="card border-0" style="background:var(--pulse-surface);border-radius:16px;">
                                <div style="height:4px;background:linear-gradient(90deg,#10b981,#059669);border-radius:16px 16px 0 0;"></div>
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <h5 class="mb-0 fw-bold">
                                            <i class="bi bi-cloud-fill me-2 text-success"></i>Mi Suscripción
                                        </h5>
                                        <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-gear me-1"></i>Gestionar
                                        </a>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-4">
                                            <div class="p-3 rounded-3 text-center" style="background:rgba(239,225,181,0.08);border:1px solid rgba(239,225,181,0.2);">
                                                <div class="text-muted mb-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Plan</div>
                                                <div class="fw-bold" style="font-size:1.4rem;color:var(--pulse-primary);">
                                                    {{ ucfirst($activeSubscription->plan_type) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="p-3 rounded-3 text-center" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);">
                                                <div class="text-muted mb-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Almacenamiento</div>
                                                <div class="fw-bold" style="font-size:1.4rem;color:#10b981;">
                                                    {{ $activeSubscription->storage_gb }} GB
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="p-3 rounded-3 text-center" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);">
                                                <div class="text-muted mb-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Renovación</div>
                                                <div class="fw-bold" style="font-size:1.2rem;color:#f59e0b;">
                                                    {{ $activeSubscription->getDaysUntilExpiration() }} días
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Uso de almacenamiento --}}
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Uso de almacenamiento</span>
                                            <span class="text-muted small">{{ number_format($activeSubscription->getStorageUsedPercentage(), 1) }}% usado</span>
                                        </div>
                                        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.1);">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $activeSubscription->getStorageUsedPercentage() }}%; background: linear-gradient(90deg, var(--pulse-primary), #c9a84c);"
                                                 aria-valuenow="{{ $activeSubscription->getStorageUsedPercentage() }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>

                                    @if($activeSubscription->isOnTrial())
                                        <div class="alert alert-info bg-info text-white border-0 mb-3">
                                            <i class="bi bi-clock me-2"></i>
                                            Estás en período de prueba. Tu suscripción se renovará automáticamente el {{ $activeSubscription->trial_ends_at->format('d/m/Y') }}.
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <div class="text-muted small">
                                            <i class="bi bi-credit-card me-1"></i>
                                            {{ number_format($activeSubscription->price, 2, ',', '.') }} €/mes
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Próximo pago: {{ $activeSubscription->next_billing_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Promoción de suscripción para usuarios sin suscripción --}}
                        <div class="col-12 mt-2">
                            <div class="card border-0" style="background:linear-gradient(135deg, rgba(239,225,181,0.05), rgba(201,168,76,0.05));border:1px solid rgba(239,225,181,0.2);border-radius:16px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="fw-bold mb-2">
                                                <i class="bi bi-cloud-plus me-2" style="color:var(--pulse-primary);"></i>
                                                Expande tu espacio en la nube
                                            </h5>
                                            <p class="text-muted mb-3">Obtén más almacenamiento para tu música e imágenes</p>
                                            <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                                                <i class="bi bi-arrow-right-circle me-1"></i>Ver Planes
                                            </a>
                                        </div>
                                        <div class="text-center">
                                            <i class="bi bi-hdd-stack display-1" style="color:var(--pulse-primary); opacity: 0.3;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
    Upload Music Modal (Pista sola o Álbum)
    ══════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="uploadMusicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-cloud-upload me-2 text-primary"></i>Subir Música
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">

                    {{-- Tabs: Pista sola / Álbum --}}
                    <div class="upload-tabs d-flex border-bottom border-secondary">
                        <button type="button" class="upload-tab active" id="tabSingle"
                            onclick="switchTab('single')">
                            <i class="bi bi-music-note me-2"></i>Pista Sola
                        </button>
                        <button type="button" class="upload-tab" id="tabAlbum" onclick="switchTab('album')">
                            <i class="bi bi-collection me-2"></i>Álbum Completo
                        </button>
                    </div>

                    {{-- Tab: Pista Sola --}}
                    <div id="singleTab" class="upload-tab-content p-4">
                        <form method="POST" action="{{ route('songs.store') }}" enctype="multipart/form-data"
                            onsubmit="return validateSingleForm()">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label text-white">Título de la canción</label>
                                    <input type="text" name="title" class="form-control bg-dark text-white border-secondary"
                                        placeholder="Ej: Noche en Gràcia" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-white">Álbum (opcional)</label>
                                    <select name="album_id" class="form-select bg-dark text-white border-secondary">
                                        <option value="">Sin álbum</option>
                                        @foreach($profile->albums ?? [] as $album)
                                            <option value="{{ $album->id }}">{{ $album->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white">Archivo de audio</label>
                                    <input type="file" name="audio_file" class="form-control bg-dark text-white border-secondary"
                                        accept="audio/*" required>
                                    <small class="text-muted">Formatos: MP3, WAV, FLAC (máx. 25MB)</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white">Portada (opcional)</label>
                                    <input type="file" name="cover_image" class="form-control bg-dark text-white border-secondary"
                                        accept="image/*">
                                    <small class="text-muted">Formatos: JPG, PNG (máx. 5MB)</small>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cloud-upload me-1"></i>Subir Canción
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tab: Álbum --}}
                    <div id="albumTab" class="upload-tab-content p-4" style="display:none;">
                        <form method="POST" action="{{ route('albums.store') }}" enctype="multipart/form-data"
                            onsubmit="return validateAlbumForm()">
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-white">Título del álbum</label>
                                    <input type="text" name="title" class="form-control bg-dark text-white border-secondary"
                                        placeholder="Ej: Untitled (Deluxe Edition)" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Año de lanzamiento</label>
                                    <input type="number" name="year" class="form-control bg-dark text-white border-secondary"
                                        placeholder="2024" min="1900" max="{{ date('Y') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-white">Portada del álbum</label>
                                    <input type="file" name="album_cover" class="form-control bg-dark text-white border-secondary"
                                        accept="image/*">
                                    <small class="text-muted">Formatos: JPG, PNG (máx. 5MB)</small>
                                </div>
                            </div>

                            {{-- Canciones del álbum --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-white mb-0">Canciones del álbum</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSongField()">
                                        <i class="bi bi-plus-circle me-1"></i>Añadir canción
                                    </button>
                                </div>
                                <div id="albumSongs" class="album-songs-container">
                                    <div class="song-item border border-secondary rounded p-3 mb-2 bg-dark">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-5">
                                                <input type="text" name="song_titles[]" class="form-control bg-dark text-white border-secondary"
                                                    placeholder="Título de la canción" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="file" name="song_files[]" class="form-control bg-dark text-white border-secondary"
                                                    accept="audio/*" required>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSongField(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-collection me-1"></i>Crear Álbum
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Switch entre tabs del modal
        function switchTab(tab) {
            const tabs = document.querySelectorAll('.upload-tab');
            const contents = document.querySelectorAll('.upload-tab-content');
            
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.style.display = 'none');
            
            if (tab === 'single') {
                document.getElementById('tabSingle').classList.add('active');
                document.getElementById('singleTab').style.display = 'block';
            } else {
                document.getElementById('tabAlbum').classList.add('active');
                document.getElementById('albumTab').style.display = 'block';
            }
        }

        // Validar formulario de pista sola
        function validateSingleForm() {
            const fileInput = document.querySelector('input[name="audio_file"]');
            const maxSize = 25 * 1024 * 1024; // 25MB
            
            if (fileInput.files[0] && fileInput.files[0].size > maxSize) {
                alert('El archivo de audio no puede superar los 25MB');
                return false;
            }
            
            return true;
        }

        // Validar formulario de álbum
        function validateAlbumForm() {
            const songFiles = document.querySelectorAll('input[name="song_files[]"]');
            const maxSize = 25 * 1024 * 1024; // 25MB
            
            for (let file of songFiles) {
                if (file.files[0] && file.files[0].size > maxSize) {
                    alert('Ningún archivo de audio puede superar los 25MB');
                    return false;
                }
            }
            
            return true;
        }

        // Añadir campo de canción al álbum
        function addSongField() {
            const container = document.getElementById('albumSongs');
            const songCount = container.children.length;
            
            const songItem = document.createElement('div');
            songItem.className = 'song-item border border-secondary rounded p-3 mb-2 bg-dark';
            songItem.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="song_titles[]" class="form-control bg-dark text-white border-secondary"
                            placeholder="Título de la canción" required>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="song_files[]" class="form-control bg-dark text-white border-secondary"
                            accept="audio/*" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSongField(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(songItem);
        }

        // Eliminar campo de canción del álbum
        function removeSongField(button) {
            const container = document.getElementById('albumSongs');
            if (container.children.length > 1) {
                button.closest('.song-item').remove();
            } else {
                alert('Debes mantener al menos una canción en el álbum');
            }
        }

        // Geolocalización para filtros
        document.addEventListener('DOMContentLoaded', function() {
            const locationBtn = document.querySelector('.btn-outline-primary');
            if (locationBtn && locationBtn.onclick && locationBtn.onclick.toString().includes('getUserLocation')) {
                // La función getUserLocation ya está definida en el partial location-button
                return;
            }
        });
    </script>

    {{-- ── MODAL AÑADIR MERCHANDISING ───────────────────── --}}
    <div class="modal fade" id="addMerchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-bag-plus me-2" style="color:#f59e0b;"></i>Añadir Producto de Merchandising</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('merch.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-white">Nombre del producto</label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Categoría</label>
                                <select name="category" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="">Selecciona categoría</option>
                                    <option value="Camisetas">Camisetas</option>
                                    <option value="Sudaderas">Sudaderas</option>
                                    <option value="Gorras">Gorras</option>
                                    <option value="Accesorios">Accesorios</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Precio (€)</label>
                                <input type="number" name="price" class="form-control bg-dark text-white border-secondary" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Ciudad</label>
                                <input type="text" name="city" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Ej: Barcelona">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Tallas (opcional)</label>
                                <input type="text" name="sizes" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Ej: S,M,L,XL">
                                <small class="text-muted">Separadas por coma</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Enlace de Merchbar</label>
                                <input type="url" name="merchbar_url" class="form-control bg-dark text-white border-secondary"
                                       placeholder="https://www.merchbar.com/..." pattern="https?://([a-zA-Z0-9-]+\.)*merchbar\..+" required>
                                <small class="text-muted">Enlace obligatorio de Merchbar</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white">Descripción</label>
                                <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white">Imagen del producto</label>
                                <input type="file" name="image" class="form-control bg-dark text-white border-secondary" 
                                       accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG (máx. 5MB)</small>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-plus-circle me-1"></i>Añadir Producto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MODAL AÑADIR CONCIERTO ───────────────────── --}}
    <div class="modal fade" id="addConcertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2" style="color:#10b981;"></i>Añadir Concierto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('concerts.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Venue</label>
                                <input type="text" name="venue" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Dirección</label>
                                <input type="text" name="address" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Calle, número, código postal" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Comunidad Autónoma</label>
                                <input type="text" name="autonomous_community" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Ej: Cataluña" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Provincia</label>
                                <input type="text" name="province" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Ej: Barcelona">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Ciudad</label>
                                <input type="text" name="city" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Género musical</label>
                                <input type="text" name="genre" class="form-control bg-dark text-white border-secondary"
                                       placeholder="Ej: Indie Rock">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Fecha</label>
                                <input type="datetime-local" name="date" class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Precio por entrada (€)</label>
                                <input type="number" name="price" class="form-control bg-dark text-white border-secondary" 
                                       step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Aforo total</label>
                                <input type="number" name="capacity" class="form-control bg-dark text-white border-secondary" 
                                       min="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white">Descripción</label>
                                <textarea name="description" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white">Enlace de Ticketmaster</label>
                                <input type="url" name="ticketmaster_url" class="form-control bg-dark text-white border-secondary"
                                       placeholder="https://www.ticketmaster.es/..." pattern="https?://([a-zA-Z0-9-]+\.)*ticketmaster\..+" required>
                                <small class="text-muted">Enlace obligatorio de Ticketmaster para venta de entradas</small>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>Añadir Concierto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funciones para gestionar merchandising
        function editMerch(id) {
            // Aquí puedes implementar la edición de merchandising
            console.log('Editar merch:', id);
        }

        function deleteMerch(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                fetch(`/merch/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el producto');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Funciones para gestionar conciertos
        function editConcert(id) {
            // Aquí puedes implementar la edición de conciertos
            console.log('Editar concierto:', id);
        }

        function deleteConcert(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este concierto?')) {
                fetch(`/concerts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el concierto');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>

    {{-- ── MODAL EDITAR FOTO DE PERFIL ───────────────────── --}}
    <div class="modal fade" id="editProfileImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-camera-fill me-2" style="color:var(--pulse-primary);"></i>Editar Foto de Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile.update-image') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="profile_image" class="form-label text-muted">Nueva foto de perfil</label>
                            <input type="file" name="profile_image" class="form-control bg-dark text-white border-secondary" 
                                   accept="image/*" required>
                            <small class="text-muted">Formatos: JPG, PNG (máx. 5MB)</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-camera-fill me-1"></i>Actualizar Foto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MODAL EDITAR PERFIL DE MÚSICO ───────────────────── --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-person-gear me-2" style="color:var(--pulse-primary);"></i>Editar Perfil de Músico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nombre</label>
                                <input type="text" name="name" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('name', Auth::user()->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Email</label>
                                <input type="email" name="email" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('email', Auth::user()->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nombre Artístico</label>
                                <input type="text" name="stage_name" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('stage_name', $profile->stage_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Género Musical</label>
                                <select name="genre" class="form-select bg-black border-secondary text-light">
                                    @foreach(['Rock','Pop','Indie','Hip Hop','Jazz','Blues','Electronic','Reggae','Metal','Classical','R&B','Soul','Folk','Country','Alternative','Punk','Funk','Otros'] as $g)
                                        <option value="{{ $g }}" {{ old('genre', $profile->genre) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Ciudad</label>
                                <input type="text" name="city" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('city', $profile->city) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Provincia</label>
                                <input type="text" name="province" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('province', $profile->province) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Comunidad Autónoma</label>
                                <select name="autonomous_community" class="form-select bg-black border-secondary text-light">
                                    @php $commMap = \App\Http\Controllers\ConcertController::getCommunitiesMap(); @endphp
                                    @foreach($commMap as $comm => $provs)
                                        <option value="{{ $comm }}" {{ old('autonomous_community', $profile->autonomous_community) === $comm ? 'selected' : '' }}>{{ $comm }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Biografía</label>
                                <textarea name="bio" class="form-control bg-black border-secondary text-light" rows="3">{{ old('bio', $profile->bio) }}</textarea>
                            </div>

                            {{-- Redes Sociales --}}
                            <div class="col-12 mt-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-share me-2" style="color:var(--pulse-primary);"></i>
                                    <span class="fw-semibold text-white">Redes Sociales</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-instagram me-1"></i>Instagram</label>
                                <input type="url" name="social_instagram" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('social_instagram', $profile->social_networks['instagram'] ?? '') }}" 
                                       placeholder="https://instagram.com/tu-perfil">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-twitter-x me-1"></i>X (Twitter)</label>
                                <input type="url" name="social_twitter" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('social_twitter', $profile->social_networks['twitter'] ?? '') }}" 
                                       placeholder="https://x.com/tu-perfil">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-facebook me-1"></i>Facebook</label>
                                <input type="url" name="social_facebook" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('social_facebook', $profile->social_networks['facebook'] ?? '') }}" 
                                       placeholder="https://facebook.com/tu-pagina">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-youtube me-1"></i>YouTube</label>
                                <input type="url" name="social_youtube" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('social_youtube', $profile->social_networks['youtube'] ?? '') }}" 
                                       placeholder="https://youtube.com/@tu-canal">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-tiktok me-1"></i>TikTok</label>
                                <input type="url" name="social_tiktok" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('social_tiktok', $profile->social_networks['tiktok'] ?? '') }}" 
                                       placeholder="https://tiktok.com/@tu-perfil">
                            </div>

                            {{-- Plataformas de Streaming --}}
                            <div class="col-12 mt-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-headphones me-2" style="color:#10b981;"></i>
                                    <span class="fw-semibold text-white">Plataformas de Streaming</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-spotify me-1"></i>Spotify</label>
                                <input type="url" name="platform_spotify" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('platform_spotify', $profile->streaming_platforms['spotify'] ?? '') }}" 
                                       placeholder="https://open.spotify.com/artist/...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-apple me-1"></i>Apple Music</label>
                                <input type="url" name="platform_apple" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('platform_apple', $profile->streaming_platforms['apple'] ?? '') }}" 
                                       placeholder="https://music.apple.com/artist/...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-soundwave me-1"></i>SoundCloud</label>
                                <input type="url" name="platform_soundcloud" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('platform_soundcloud', $profile->streaming_platforms['soundcloud'] ?? '') }}" 
                                       placeholder="https://soundcloud.com/tu-perfil">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted"><i class="bi bi-music-note-list me-1"></i>Deezer</label>
                                <input type="url" name="platform_deezer" class="form-control bg-black border-secondary text-light" 
                                       value="{{ old('platform_deezer', $profile->streaming_platforms['deezer'] ?? '') }}" 
                                       placeholder="https://deezer.com/artist/...">
                            </div>

                            <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
