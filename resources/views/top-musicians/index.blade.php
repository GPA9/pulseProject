<x-app-layout>
    <style>
        .podium-section {
            padding: 3rem 0 4rem;
        }

        /* ── Pedestal bars ───────────────────── */
        .podium-wrapper {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 0;
        }

        .podium-slot {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Artist card above pedestal */
        .podium-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 1.5rem 1rem;
            border-radius: 16px 16px 0 0;
            width: 220px;
            position: relative;
            transition: transform 0.3s ease;
        }
        .podium-card:hover {
            transform: translateY(-6px);
        }

        /* Gold – 1st */
        .podium-1 .podium-card {
            background: linear-gradient(160deg, rgba(212,175,55,0.18) 0%, rgba(212,175,55,0.04) 100%);
            border: 1px solid rgba(212,175,55,0.45);
            box-shadow: 0 0 40px rgba(212,175,55,0.15), 0 8px 32px rgba(0,0,0,0.5);
        }
        .podium-1 .pedestal {
            background: linear-gradient(180deg, rgba(212,175,55,0.25) 0%, rgba(212,175,55,0.08) 100%);
            border: 1px solid rgba(212,175,55,0.35);
            border-bottom: none;
        }
        .podium-1 .rank-badge {
            background: linear-gradient(135deg, #d4af37, #f5d572, #b8860b);
            color: #1a1200;
            box-shadow: 0 0 20px rgba(212,175,55,0.6);
        }
        .podium-1 .plays-count { color: #d4af37; }
        .podium-1 .artist-img-ring {
            border-color: rgba(212,175,55,0.7);
            box-shadow: 0 0 0 4px rgba(212,175,55,0.15);
        }

        /* Silver – 2nd */
        .podium-2 .podium-card {
            background: linear-gradient(160deg, rgba(180,180,190,0.14) 0%, rgba(180,180,190,0.03) 100%);
            border: 1px solid rgba(180,180,190,0.35);
            box-shadow: 0 0 30px rgba(180,180,190,0.1), 0 8px 24px rgba(0,0,0,0.4);
        }
        .podium-2 .pedestal {
            background: linear-gradient(180deg, rgba(180,180,190,0.2) 0%, rgba(180,180,190,0.06) 100%);
            border: 1px solid rgba(180,180,190,0.25);
            border-bottom: none;
        }
        .podium-2 .rank-badge {
            background: linear-gradient(135deg, #9e9ea8, #d0d0d8, #7a7a84);
            color: #111;
            box-shadow: 0 0 16px rgba(180,180,190,0.45);
        }
        .podium-2 .plays-count { color: #b4b4be; }
        .podium-2 .artist-img-ring {
            border-color: rgba(180,180,190,0.55);
            box-shadow: 0 0 0 4px rgba(180,180,190,0.12);
        }

        /* Bronze – 3rd */
        .podium-3 .podium-card {
            background: linear-gradient(160deg, rgba(176,115,65,0.14) 0%, rgba(176,115,65,0.03) 100%);
            border: 1px solid rgba(176,115,65,0.3);
            box-shadow: 0 0 24px rgba(176,115,65,0.1), 0 8px 20px rgba(0,0,0,0.4);
        }
        .podium-3 .pedestal {
            background: linear-gradient(180deg, rgba(176,115,65,0.18) 0%, rgba(176,115,65,0.05) 100%);
            border: 1px solid rgba(176,115,65,0.25);
            border-bottom: none;
        }
        .podium-3 .rank-badge {
            background: linear-gradient(135deg, #b07341, #d4935a, #8b5a2b);
            color: #fff;
            box-shadow: 0 0 14px rgba(176,115,65,0.4);
        }
        .podium-3 .plays-count { color: #c08040; }
        .podium-3 .artist-img-ring {
            border-color: rgba(176,115,65,0.5);
            box-shadow: 0 0 0 4px rgba(176,115,65,0.1);
        }

        /* Pedestal base */
        .pedestal {
            width: 220px;
            border-radius: 0 0 8px 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pedestal-inner {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 700;
            opacity: 0.6;
            padding: 0.5rem 0;
        }

        /* Height of pedestal */
        .podium-1 .pedestal { height: 90px; }
        .podium-2 .pedestal { height: 60px; }
        .podium-3 .pedestal { height: 40px; }

        /* Artist image */
        .artist-img-ring {
            border-radius: 50%;
            border: 2px solid transparent;
            padding: 3px;
            margin-bottom: 0.85rem;
        }
        .artist-img-ring img,
        .artist-img-ring .artist-img-placeholder {
            border-radius: 50%;
            display: block;
        }
        .artist-img-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.06);
        }

        /* Rank badge */
        .rank-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 0.72rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0;
        }

        /* Table enhancements */
        .rank-table tbody tr:hover {
            background: rgba(255,255,255,0.03) !important;
        }

        /* Shine divider */
        .shine-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(212,175,55,0.4) 30%, rgba(139,92,246,0.4) 70%, transparent 100%);
            margin: 3rem 0 2.5rem;
        }

        @media (max-width: 767px) {
            .podium-wrapper { gap: 0; }
            .podium-card { width: 110px; padding: 1rem 0.5rem 0.75rem; }
            .pedestal { width: 110px; }
            .podium-1 .pedestal { height: 60px; }
            .podium-2 .pedestal { height: 40px; }
            .podium-3 .pedestal { height: 28px; }
        }
    </style>

    <div class="container py-5">

        {{-- Header --}}
        <div class="text-center mb-5">
            <p class="text-muted mb-2" style="font-size:0.72rem;letter-spacing:4px;text-transform:uppercase;">Clasificación Global</p>
            <h1 class="display-5 fw-bold mb-0">Top 20 <span style="color:var(--pulse-primary);">Artistas</span></h1>
            <p class="text-muted mt-2">Los músicos más reproducidos en Pulse</p>
        </div>

        {{-- ── Podium ─────────────────────────────────────────────── --}}
        @if($musicians->count() >= 3)
        <div class="podium-section">
            <div class="podium-wrapper">

                {{-- 2nd place --}}
                @php $m2 = $musicians[1]; @endphp
                <div class="podium-slot podium-2 me-1 me-md-2">
                    <a href="{{ route('musicians.show', $m2->id) }}" class="text-decoration-none podium-card">
                        <div class="rank-badge">2</div>

                        <div class="artist-img-ring">
                            @if($m2->image_path)
                                @if(file_exists(public_path('images/band-logos/' . $m2->image_path)))
                                    <img src="{{ asset('images/band-logos/' . $m2->image_path) }}"
                                @else
                                    <img src="{{ asset('storage/' . $m2->image_path) }}"
                                @endif
                                    style="width:76px;height:76px;object-fit:cover;"
                                    alt="{{ $m2->stage_name }}">
                            @else
                                <div class="artist-img-placeholder" style="width:76px;height:76px;">
                                    <i class="bi bi-person-fill fs-3 text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <div class="fw-bold text-white text-center" style="font-size:0.95rem;line-height:1.2;">{{ $m2->stage_name }}</div>
                        <div class="text-muted mt-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">{{ $m2->genre }}</div>
                        <div class="plays-count fw-bold mt-2" style="font-size:1.1rem;">{{ number_format($m2->total_plays) }}</div>
                        <div class="text-muted" style="font-size:0.65rem;letter-spacing:2px;text-transform:uppercase;">reproducciones</div>
                    </a>
                    <div class="pedestal"><span class="pedestal-inner text-muted">Plata</span></div>
                </div>

                {{-- 1st place --}}
                @php $m1 = $musicians[0]; @endphp
                <div class="podium-slot podium-1 me-1 me-md-2" style="z-index:2;">
                    <a href="{{ route('musicians.show', $m1->id) }}" class="text-decoration-none podium-card" style="padding-top:2rem;">
                        <div class="rank-badge">1</div>

                        {{-- Crown icon (no emoji) --}}
                        <div class="mb-2" style="color:#d4af37;font-size:1.5rem;line-height:1;">
                            <svg width="36" height="28" viewBox="0 0 36 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 24h32M2 24L6 10l8 8L18 4l4 14 8-8 4 14" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                                <circle cx="2" cy="10" r="2" fill="currentColor"/>
                                <circle cx="18" cy="4" r="2" fill="currentColor"/>
                                <circle cx="34" cy="10" r="2" fill="currentColor"/>
                            </svg>
                        </div>

                        <div class="artist-img-ring">
                            @if($m1->image_path)
                                @if(file_exists(public_path('images/band-logos/' . $m1->image_path)))
                                    <img src="{{ asset('images/band-logos/' . $m1->image_path) }}"
                                @else
                                    <img src="{{ asset('storage/' . $m1->image_path) }}"
                                @endif
                                    style="width:100px;height:100px;object-fit:cover;"
                                    alt="{{ $m1->stage_name }}">
                            @else
                                <div class="artist-img-placeholder" style="width:100px;height:100px;">
                                    <i class="bi bi-person-fill fs-2 text-warning"></i>
                                </div>
                            @endif
                        </div>

                        <div class="fw-bold text-white text-center" style="font-size:1.05rem;line-height:1.2;">{{ $m1->stage_name }}</div>
                        <div class="text-muted mt-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">{{ $m1->genre }}</div>
                        <div class="plays-count fw-bold mt-2" style="font-size:1.35rem;">{{ number_format($m1->total_plays) }}</div>
                        <div class="text-muted" style="font-size:0.65rem;letter-spacing:2px;text-transform:uppercase;">reproducciones</div>
                    </a>
                    <div class="pedestal"><span class="pedestal-inner text-muted">Oro</span></div>
                </div>

                {{-- 3rd place --}}
                @php $m3 = $musicians[2]; @endphp
                <div class="podium-slot podium-3">
                    <a href="{{ route('musicians.show', $m3->id) }}" class="text-decoration-none podium-card">
                        <div class="rank-badge">3</div>

                        <div class="artist-img-ring">
                            @if($m3->image_path)
                                @if(file_exists(public_path('images/band-logos/' . $m3->image_path)))
                                    <img src="{{ asset('images/band-logos/' . $m3->image_path) }}"
                                @else
                                    <img src="{{ asset('storage/' . $m3->image_path) }}"
                                @endif
                                    style="width:76px;height:76px;object-fit:cover;"
                                    alt="{{ $m3->stage_name }}">
                            @else
                                <div class="artist-img-placeholder" style="width:76px;height:76px;">
                                    <i class="bi bi-person-fill fs-3 text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <div class="fw-bold text-white text-center" style="font-size:0.95rem;line-height:1.2;">{{ $m3->stage_name }}</div>
                        <div class="text-muted mt-1" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">{{ $m3->genre }}</div>
                        <div class="plays-count fw-bold mt-2" style="font-size:1.1rem;">{{ number_format($m3->total_plays) }}</div>
                        <div class="text-muted" style="font-size:0.65rem;letter-spacing:2px;text-transform:uppercase;">reproducciones</div>
                    </a>
                    <div class="pedestal"><span class="pedestal-inner text-muted">Bronce</span></div>
                </div>

            </div>

            {{-- Ground line --}}
            <div style="height:3px;background:linear-gradient(90deg,transparent,rgba(212,175,55,0.25) 25%,rgba(139,92,246,0.25) 75%,transparent);border-radius:2px;max-width:680px;margin:0 auto;"></div>
        </div>
        @endif

        <div class="shine-divider"></div>

        {{-- Full Table --}}
        <div class="card border-0" style="background:var(--pulse-surface);border-radius:16px;">
            <div style="height:3px;background:linear-gradient(90deg,var(--pulse-primary),#8b5cf6);border-radius:16px 16px 0 0;"></div>
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">
                    <i class="bi bi-list-ol me-2 text-primary"></i>Clasificación completa
                </h5>

                <div class="table-responsive">
                    <table class="table table-dark rank-table align-middle mb-0" style="font-size:0.9rem;border-collapse:separate;border-spacing:0 2px;">
                        <thead>
                            <tr style="border-bottom:2px solid var(--pulse-border);">
                                <th class="border-0 text-muted fw-bold ps-3" style="font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;width:52px;">#</th>
                                <th class="border-0 text-muted fw-bold" style="font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;">Artista</th>
                                <th class="border-0 text-muted fw-bold d-none d-md-table-cell" style="font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;">Género</th>
                                <th class="border-0 text-muted fw-bold d-none d-md-table-cell" style="font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;">Ciudad</th>
                                <th class="border-0 text-muted fw-bold text-end pe-3" style="font-size:0.68rem;letter-spacing:2px;text-transform:uppercase;">Reproducciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($musicians as $index => $musician)
                            @php
                                $maxPlays = $musicians->first()->total_plays;
                                $pct = $maxPlays > 0 ? ($musician->total_plays / $maxPlays * 100) : 0;
                            @endphp
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                <td class="border-0 ps-3" style="width:52px;">
                                    @if($index === 0)
                                        <span class="fw-black" style="color:#d4af37;font-size:1rem;">1</span>
                                    @elseif($index === 1)
                                        <span class="fw-black" style="color:#b4b4be;font-size:1rem;">2</span>
                                    @elseif($index === 2)
                                        <span class="fw-black" style="color:#c08040;font-size:1rem;">3</span>
                                    @else
                                        <span class="text-muted">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="border-0">
                                    <a href="{{ route('musicians.show', $musician->id) }}"
                                       class="d-flex align-items-center gap-3 text-decoration-none text-white">
                                        @if($musician->image_path)
                                            @if(file_exists(public_path('images/band-logos/' . $musician->image_path)))
                                                <img src="{{ asset('images/band-logos/' . $musician->image_path) }}"
                                            @else
                                                <img src="{{ asset('storage/' . $musician->image_path) }}"
                                            @endif
                                                class="rounded-circle flex-shrink-0"
                                                style="width:38px;height:38px;object-fit:cover;border:1.5px solid rgba(255,255,255,0.1);"
                                                alt="{{ $musician->stage_name }}">
                                        @else
                                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                                style="width:38px;height:38px;background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.1);">
                                                <i class="bi bi-person-fill text-muted" style="font-size:0.85rem;"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $musician->stage_name }}</span>
                                    </a>
                                </td>
                                <td class="border-0 text-muted d-none d-md-table-cell" style="font-size:0.85rem;">{{ $musician->genre ?? '—' }}</td>
                                <td class="border-0 text-muted d-none d-md-table-cell" style="font-size:0.85rem;">{{ $musician->city ?? '—' }}</td>
                                <td class="border-0 text-end pe-3">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <div class="d-none d-lg-block" style="width:90px;">
                                            <div class="progress" style="height:3px;background:rgba(255,255,255,0.07);border-radius:4px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width:{{ $pct }}%;background:linear-gradient(90deg,var(--pulse-primary),#8b5cf6);border-radius:4px;"
                                                    aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <span class="fw-bold" style="color:var(--pulse-primary);min-width:70px;text-align:right;">
                                            {{ number_format($musician->total_plays) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="border-0 text-center py-5 text-muted">
                                    <i class="bi bi-music-note-beamed fs-1 d-block mb-2 opacity-25"></i>
                                    Aún no hay reproducciones registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>