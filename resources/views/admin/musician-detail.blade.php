<x-app-layout>
    <div class="container-fluid py-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold text-white mb-1">
                    @if($musician->image_path)
                        <img src="{{ asset('images/band-logos/' . $musician->image_path) }}" class="rounded-circle me-2" style="width:48px;height:48px;object-fit:cover;" alt="">
                    @endif
                    {{ $musician->stage_name }}
                </h2>
                <p class="text-white-50 mb-0">{{ $musician->genre }} • {{ $musician->city }} • {{ $musician->user->email }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.musicians') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
                <form action="{{ route('admin.musicians.destroy', $musician->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este perfil de músico y todo su contenido?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Eliminar Perfil</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert border-0 mb-4" style="background:rgba(16,185,129,0.15);color:#34d399;border-radius:12px;">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- Estadísticas del músico --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="p-3 rounded-3 text-center" style="background:var(--pulse-surface);border:1px solid rgba(255,255,255,0.08);">
                    <div class="text-white-50 small mb-1">Canciones</div>
                    <div class="fw-bold fs-4 text-white">{{ $stats['total_songs'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded-3 text-center" style="background:var(--pulse-surface);border:1px solid rgba(255,255,255,0.08);">
                    <div class="text-white-50 small mb-1">Conciertos</div>
                    <div class="fw-bold fs-4 text-white">{{ $stats['total_concerts'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded-3 text-center" style="background:var(--pulse-surface);border:1px solid rgba(255,255,255,0.08);">
                    <div class="text-white-50 small mb-1">Merch</div>
                    <div class="fw-bold fs-4 text-white">{{ $stats['total_merch'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded-3 text-center" style="background:var(--pulse-surface);border:1px solid rgba(255,255,255,0.08);">
                    <div class="text-white-50 small mb-1">Reproducciones</div>
                    <div class="fw-bold fs-4" style="color:var(--pulse-primary);">{{ number_format($stats['total_plays']) }}</div>
                </div>
            </div>
        </div>

        {{-- Suscripción activa --}}
        @php $activeSub = $musician->subscriptions()->active()->first(); @endphp
        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-credit-card me-2" style="color:#10b981;"></i>Suscripción</h5>
                @if($activeSub)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-white-50 small">Plan</div>
                            <div class="fw-bold text-white">{{ ucfirst($activeSub->plan_type) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white-50 small">Precio</div>
                            <div class="fw-bold" style="color:var(--pulse-primary);">{{ number_format($activeSub->price, 2, ',', '.') }} €/mes</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white-50 small">Almacenamiento</div>
                            <div class="fw-bold text-white">{{ $activeSub->storage_gb }} GB</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-white-50 small">Renovación</div>
                            <div class="fw-bold text-white">{{ $activeSub->ends_at?->format('d/m/Y') ?? '—' }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-white-50">Sin suscripción activa</p>
                @endif
            </div>
        </div>

        {{-- Canciones --}}
        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-music-note-beamed me-2" style="color:var(--pulse-primary);"></i>Canciones ({{ $musician->songs->count() }})</h5>
                @if($musician->songs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-borderless align-middle mb-0">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th class="text-white-50 small">Título</th>
                                    <th class="text-white-50 small">Álbum</th>
                                    <th class="text-white-50 small text-end">Reproducciones</th>
                                    <th class="text-white-50 small text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($musician->songs as $song)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td class="text-white">{{ $song->title }}</td>
                                    <td class="text-white-50">{{ $song->album?->title ?? '—' }}</td>
                                    <td class="text-end text-white">{{ number_format($song->play_count) }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.songs.destroy', $song->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta canción?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white-50">Sin canciones</p>
                @endif
            </div>
        </div>

        {{-- Conciertos --}}
        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-calendar-event me-2" style="color:#10b981;"></i>Conciertos ({{ $musician->concerts->count() }})</h5>
                @if($musician->concerts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-borderless align-middle mb-0">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th class="text-white-50 small">Fecha</th>
                                    <th class="text-white-50 small">Venue</th>
                                    <th class="text-white-50 small">Ciudad</th>
                                    <th class="text-white-50 small text-end">Precio</th>
                                    <th class="text-white-50 small text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($musician->concerts as $concert)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td class="text-white">{{ $concert->date->format('d/m/Y H:i') }}</td>
                                    <td class="text-white">{{ $concert->venue }}</td>
                                    <td class="text-white-50">{{ $concert->city }}</td>
                                    <td class="text-end fw-bold" style="color:var(--pulse-primary);">{{ number_format($concert->price, 2, ',', '.') }} €</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.concerts.destroy', $concert->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este concierto?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white-50">Sin conciertos</p>
                @endif
            </div>
        </div>

        {{-- Merchandising --}}
        <div class="card border-0 mb-4" style="background:var(--pulse-surface);border-radius:16px;">
            <div class="card-body p-4">
                <h5 class="fw-bold text-white mb-3"><i class="bi bi-bag me-2" style="color:#f59e0b;"></i>Merchandising ({{ $musician->merch->count() }})</h5>
                @if($musician->merch->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-borderless align-middle mb-0">
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th class="text-white-50 small">Producto</th>
                                    <th class="text-white-50 small">Categoría</th>
                                    <th class="text-white-50 small text-end">Precio</th>
                                    <th class="text-white-50 small text-end">Ventas</th>
                                    <th class="text-white-50 small text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($musician->merch as $item)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td class="text-white">{{ $item->name }}</td>
                                    <td class="text-white-50">{{ $item->category }}</td>
                                    <td class="text-end fw-bold" style="color:var(--pulse-primary);">{{ number_format($item->price, 2, ',', '.') }} €</td>
                                    <td class="text-end text-white">{{ $item->sales_count }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.merch.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este producto?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-white-50">Sin merchandising</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
