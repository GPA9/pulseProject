<x-app-layout>

    @php

        // Check if musician has active subscription

        $hasActiveSubscription = $musician->subscriptions()->active()->exists();

    @endphp

    

    <!-- Hero Banner -->

    <div class="position-relative mb-5" style="margin-top: -30px;">

        <div class="bg-dark" style="height: 400px; overflow: hidden;">

            @if($musician->image_path)

                @if(file_exists(public_path('images/band-logos/' . $musician->image_path)))

                    <img src="{{ asset('images/band-logos/' . $musician->image_path) }}" alt="{{ $musician->stage_name }}"

                @else

                    <img src="{{ asset('storage/' . $musician->image_path) }}" alt="{{ $musician->stage_name }}"

                @endif

                    class="w-100 h-100" style="object-fit: cover; opacity: 0.4; filter: blur(20px);">

            @else

                <div class="w-100 h-100 bg-gradient-hero"></div>

            @endif

            <div class="position-absolute bottom-0 start-0 w-100 bg-gradient-to-t from-black p-5">

                <div class="container">

                    <div class="d-flex align-items-end gap-4">

                        @if($musician->image_path)

                            @if(file_exists(public_path('images/band-logos/' . $musician->image_path)))

                                <img src="{{ asset('images/band-logos/' . $musician->image_path) }}" alt="{{ $musician->stage_name }}"

                            @else

                                <img src="{{ asset('storage/' . $musician->image_path) }}" alt="{{ $musician->stage_name }}"

                            @endif

                                class="rounded-circle shadow-lg border border-4 border-dark d-none d-md-block"

                                style="width: 200px; height: 200px; object-fit: cover;">

                        @else

                            <div class="rounded-circle bg-dark border border-4 border-secondary shadow-lg d-none d-md-block"

                                style="width:200px; height:200px; position:relative; flex-shrink:0;">

                                <i class="bi bi-person-fill text-muted"

                                    style="font-size:5rem; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); line-height:1;"></i>

                            </div>

                        @endif

                        <div class="mb-2">

                            <span class="badge bg-primary mb-2 text-black fw-bold"><i

                                    class="bi bi-patch-check-fill me-1"></i> Artista Verificado</span>

                            <h1 class="display-2 fw-bold text-white mb-1">{{ $musician->stage_name }}</h1>

                            <p class="lead text-light opacity-75 mb-3">{{ $musician->genre }} • {{ $musician->city }}

                            </p>

                            <p class="text-light opacity-75" style="max-width: 600px;">{{ $musician->bio }}</p>

                            {{-- Redes Sociales --}}

                            @if($musician->social_networks)

                            <div class="d-flex gap-2 mt-3 flex-wrap">

                                @if(!empty($musician->social_networks['instagram']))

                                    <a href="{{ $musician->social_networks['instagram'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(225,48,108,0.15);"><i class="bi bi-instagram"></i></a>

                                @endif

                                @if(!empty($musician->social_networks['twitter']))

                                    <a href="{{ $musician->social_networks['twitter'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(255,255,255,0.1);"><i class="bi bi-twitter-x"></i></a>

                                @endif

                                @if(!empty($musician->social_networks['facebook']))

                                    <a href="{{ $musician->social_networks['facebook'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(24,119,242,0.15);"><i class="bi bi-facebook"></i></a>

                                @endif

                                @if(!empty($musician->social_networks['youtube']))

                                    <a href="{{ $musician->social_networks['youtube'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(255,0,0,0.15);"><i class="bi bi-youtube"></i></a>

                                @endif

                                @if(!empty($musician->social_networks['tiktok']))

                                    <a href="{{ $musician->social_networks['tiktok'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(255,255,255,0.1);"><i class="bi bi-tiktok"></i></a>

                                @endif

                            </div>

                            @endif

                            {{-- Plataformas de Streaming --}}

                            @if($musician->streaming_platforms)

                            <div class="d-flex gap-2 mt-2 flex-wrap">

                                @if(!empty($musician->streaming_platforms['spotify']))

                                    <a href="{{ $musician->streaming_platforms['spotify'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(30,215,96,0.15);"><i class="bi bi-spotify me-1"></i>Spotify</a>

                                @endif

                                @if(!empty($musician->streaming_platforms['apple']))

                                    <a href="{{ $musician->streaming_platforms['apple'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(252,60,68,0.15);"><i class="bi bi-apple me-1"></i>Apple</a>

                                @endif

                                @if(!empty($musician->streaming_platforms['soundcloud']))

                                    <a href="{{ $musician->streaming_platforms['soundcloud'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(255,85,0,0.15);"><i class="bi bi-soundwave me-1"></i>SoundCloud</a>

                                @endif

                                @if(!empty($musician->streaming_platforms['deezer']))

                                    <a href="{{ $musician->streaming_platforms['deezer'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-light border-0" style="background:rgba(160,54,255,0.15);"><i class="bi bi-music-note-list me-1"></i>Deezer</a>

                                @endif

                            </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="container pb-5">

        <div class="row g-5">

            <!-- Songs Column -->

            <div class="col-lg-8">



                {{-- ── Álbumes ────────────────────────────────────── --}}

                @if($musician->albums->count() > 0)

                    <div class="mb-5">

                        <h3 class="fw-bold mb-4"><i class="bi bi-collection me-2 text-primary"></i>Álbumes</h3>

                        <div class="row g-3">

                            @foreach($musician->albums as $album)

                                <div class="col-sm-6 col-md-4">

                                    <div class="card bg-dark border-secondary h-100"

                                        style="border-radius:12px; overflow:hidden;">

                                        @if($album->cover_path)

                                            <img src="{{ asset('storage/' . $album->cover_path) }}" class="card-img-top"

                                                style="height:140px; object-fit:cover;" alt="{{ $album->title }}">

                                        @else

                                            <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-25"

                                                style="height:140px;">

                                                <i class="bi bi-collection-play fs-2 text-muted"></i>

                                            </div>

                                        @endif

                                        <div class="card-body p-3">

                                            <h6 class="fw-bold text-white mb-1">{{ $album->title }}</h6>

                                            <small class="text-muted">

                                                {{ $album->songs->count() }} pista(s)

                                                @if($album->release_year) · {{ $album->release_year }}@endif

                                            </small>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif



                {{-- ── Pistas ─────────────────────────────────────── --}}

                <div>

                    <div class="d-flex align-items-center justify-content-between mb-4">

                        <h3 class="fw-bold m-0"><i class="bi bi-music-note-list me-2 text-primary"></i>Pistas Populares

                        </h3>

                        <span class="badge bg-secondary bg-opacity-25 text-light">{{ $musician->songs->count() }}

                            pistas</span>

                    </div>



                    @if($musician->songs->count() > 0)

                        {{-- Songs grouped by album --}}

                        @php

                            $soloSongs = $musician->songs->whereNull('album_id');

                        @endphp



                        {{-- Album songs --}}

                        @foreach($musician->albums as $album)

                            @if($album->songs->count() > 0)

                                <div class="mb-4">

                                    <div class="d-flex align-items-center gap-2 mb-2 pb-1 border-bottom border-secondary">

                                        <i class="bi bi-collection text-primary" style="font-size:0.85rem;"></i>

                                        <span

                                            class="small text-muted fw-bold text-uppercase tracking-wide">{{ $album->title }}</span>

                                    </div>

                                    <div class="list-group list-group-flush bg-transparent">

                                        @foreach($album->songs as $index => $song)

                                            <div

                                                class="list-group-item bg-transparent border-bottom border-secondary text-light py-3 px-2 d-flex align-items-center hover-bg-surface rounded">

                                                <span class="text-muted me-3" style="width: 20px;">{{ $index + 1 }}</span>

                                                <div class="me-3 position-relative">

                                                    @if($song->cover_path)

                                                        <img src="{{ asset('storage/' . $song->cover_path) }}" class="rounded" width="40"

                                                            height="40" alt="Portada">

                                                    @elseif($album->cover_path)

                                                        <img src="{{ asset('storage/' . $album->cover_path) }}" class="rounded" width="40"

                                                            height="40" alt="Portada">

                                                    @else

                                                        <div class="bg-dark border border-secondary rounded d-flex align-items-center justify-content-center"

                                                            style="width: 40px; height: 40px;">

                                                            <i class="bi bi-music-note-beamed text-muted"></i>

                                                        </div>

                                                    @endif

                                                </div>

                                                <div class="flex-grow-1">

                                                    <h6 class="mb-0 fw-bold">{{ $song->title }}</h6>

                                                    <small class="text-muted song-play-label" id="plays-{{ $song->id }}">{{ number_format($song->play_count) }}

                                                        reproducciones</small>

                                                </div>

                                                <div class="ms-3">

                                                    @if($hasActiveSubscription)

                                                        <audio controls class="d-none d-md-block" style="height: 30px;"

                                                            data-song-id="{{ $song->id }}">

                                                            <source src="{{ asset('storage/' . $song->file_path) }}" type="audio/mpeg">

                                                        </audio>

                                                    @else

                                                        <div class="alert alert-warning alert-sm mb-0" style="padding:0.4rem 0.6rem;font-size:0.75rem;">

                                                            <i class="bi bi-lock me-1"></i>Requiere suscripción activa

                                                        </div>

                                                    @endif

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                        @endforeach



                        {{-- Pistas sueltas --}}

                        @if($soloSongs->count() > 0)

                            <div class="mb-4">

                                @if($musician->albums->count() > 0)

                                    <div class="d-flex align-items-center gap-2 mb-2 pb-1 border-bottom border-secondary">

                                        <i class="bi bi-music-note text-primary" style="font-size:0.85rem;"></i>

                                        <span class="small text-muted fw-bold text-uppercase">Pistas sueltas</span>

                                    </div>

                                @endif

                                <div class="list-group list-group-flush bg-transparent">

                                    @foreach($soloSongs as $index => $song)

                                        <div

                                            class="list-group-item bg-transparent border-bottom border-secondary text-light py-3 px-2 d-flex align-items-center hover-bg-surface rounded">

                                            <span class="text-muted me-3" style="width: 20px;">{{ $index + 1 }}</span>

                                            <div class="me-3 position-relative">

                                                @if($song->cover_path)

                                                    <img src="{{ asset('storage/' . $song->cover_path) }}" class="rounded" width="40"

                                                        height="40" alt="Portada">

                                                @else

                                                    <div class="bg-dark border border-secondary rounded d-flex align-items-center justify-content-center"

                                                        style="width: 40px; height: 40px;">

                                                        <i class="bi bi-music-note-beamed text-muted"></i>

                                                    </div>

                                                @endif

                                            </div>

                                            <div class="flex-grow-1">

                                                <h6 class="mb-0 fw-bold">{{ $song->title }}</h6>

                                                <small class="text-muted song-play-label" id="plays-{{ $song->id }}">{{ number_format($song->play_count) }}

                                                    reproducciones</small>

                                            </div>

                                            <div class="ms-3">

                                                <audio controls class="d-none d-md-block" style="height: 30px;"

                                                    data-song-id="{{ $song->id }}">

                                                    <source src="{{ asset('storage/' . $song->file_path) }}" type="audio/mpeg">

                                                </audio>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif

                    @else

                        <div class="text-center py-5 border border-dashed border-secondary rounded">

                            <i class="bi bi-music-note text-muted fs-1 mb-3"></i>

                            <p class="text-muted">Aún no hay pistas subidas.</p>

                        </div>

                    @endif

                </div>

            </div>



            <!-- Sidebar -->

            <div class="col-lg-4">

                <h4 class="fw-bold mb-4">Próximos Conciertos</h4>

                @php $upcomingConcerts = $musician->concerts->where('date', '>=', now())->sortBy('date'); @endphp

                @if($upcomingConcerts->count() > 0)

                    <div class="d-flex flex-column gap-3">

                        @foreach($upcomingConcerts as $concert)

                            <div class="card bg-dark border-secondary">

                                <div class="card-body d-flex align-items-center gap-3">

                                    <div class="text-center bg-surface rounded p-2" style="min-width: 60px;">

                                        <span

                                            class="d-block text-primary fw-bold small">{{ $concert->date->locale('es')->isoFormat('MMM') }}</span>

                                        <span class="d-block text-white fw-bold fs-4">{{ $concert->date->format('d') }}</span>

                                    </div>

                                    <div class="flex-grow-1">

                                        <h6 class="mb-1 fw-bold text-white">{{ $concert->venue }}</h6>

                                        <small class="text-muted d-block mb-1">{{ $concert->city }}</small>

                                    </div>

                                    <a href="{{ $concert->ticketmaster_url }}" target="_blank" rel="noopener noreferrer"

                                        class="btn btn-outline-light btn-sm rounded-pill">Entradas</a>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="text-muted">No hay conciertos próximos.</p>

                @endif



                <div class="mt-5">

                    <h4 class="fw-bold mb-4">Merch</h4>

                    @if($musician->merch->count() > 0)

                        <div class="d-flex flex-column gap-2">

                            @foreach($musician->merch->take(3) as $item)

                                <div class="d-flex align-items-center gap-3 p-2 rounded bg-dark border border-secondary">

                                    <div class="text-center rounded d-flex align-items-center justify-content-center bg-secondary bg-opacity-25"

                                        style="width:44px;height:44px;flex-shrink:0;">

                                        <i class="bi bi-bag-fill text-muted"></i>

                                    </div>

                                    <div class="flex-grow-1 min-width-0">

                                        <div class="fw-semibold text-white small">{{ $item->name }}</div>

                                        <small class="text-primary">{{ number_format($item->price, 2, ',', '.') }} €</small>

                                    </div>

                                </div>

                            @endforeach

                            @if($musician->merch->count() > 3)

                                <a href="{{ route('merch.index') }}?search={{ urlencode($musician->stage_name) }}"

                                    class="btn btn-outline-secondary btn-sm mt-1">

                                    Ver todo el merch

                                </a>

                            @endif

                        </div>

                    @else

                        <p class="text-muted">Merchandising próximamente.</p>

                    @endif

                </div>

            </div>

        </div>

    </div>



    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

            // Track which songs we've already incremented in this session

            const played = new Set();



            document.querySelectorAll('audio[data-song-id]').forEach(function (audio) {

                audio.addEventListener('play', function () {

                    const songId = this.dataset.songId;

                    // Only count once per page load per song

                    if (played.has(songId)) return;

                    played.add(songId);



                    fetch('/songs/' + songId + '/play', {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': csrfToken,

                            'Accept': 'application/json',

                        }

                    })

                    .then(res => res.json())

                    .then(data => {

                        if (data.success) {

                            // Actualizar el contador en la UI inmediatamente

                            const label = document.getElementById('plays-' + songId);

                            if (label) {

                                label.textContent = data.play_count.toLocaleString('es-ES') + ' reproducciones';

                            }

                        }

                    })

                    .catch(err => console.error('Error registrando reproducción:', err));

                });

            });

        });

    </script>



</x-app-layout>