<x-app-layout>
<div class="radio-page">

    {{-- ── Header ─────────────────────────────────────── --}}
    <div class="text-center pt-5 pb-3">
        <h1 class="fw-bold mb-1" style="font-size:2.2rem; letter-spacing:-0.5px;">Radio <span style="color:var(--pulse-primary);">Pulse</span></h1>
        <p class="text-muted mb-0" style="font-size:0.95rem;">Sintoniza la música independiente de tu comunidad</p>
    </div>

    {{-- ── Filtros de comunidad / provincia ─────────────── --}}
    <div class="container-fluid">
        <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
            <div>
                <label class="filter-label">Comunidad</label>
                <select class="filter-select" id="communitySelect" onchange="onCommunityChange(this.value)">
                    <option value="">Selecciona comunidad…</option>
                    @foreach($communities as $community => $provinces)
                        <option value="{{ $community }}">{{ $community }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="filter-label">Provincia <span class="text-muted" style="font-size:0.7rem;">(opcional)</span></label>
                <select class="filter-select" id="provinceSelect" disabled onchange="onProvinceChange(this.value)">
                    <option value="">— Todas —</option>
                </select>
            </div>
        </div>

        {{-- ── Layout dividido en 2 columnas ────────────────────── --}}
        <div class="row">
            {{-- Columna izquierda: Player --}}
            <div class="col-lg-4 col-md-5">
                <div class="player-card">

                    {{-- Album art / visualizer --}}
                    <div class="player-art" id="playerArt">
                        <div class="art-idle" id="artIdle">
                            <i class="bi bi-broadcast" style="font-size:3rem;color:var(--pulse-primary);opacity:0.5;"></i>
                        </div>
                        <div class="art-playing d-none" id="artPlaying">
                            <div class="vinyl" id="vinyl">
                                <div class="vinyl-center"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Track info --}}
                    <div class="player-info text-center mb-3">
                        <div class="track-label" id="trackLabel">Selecciona una comunidad</div>
                        <div class="track-title" id="trackTitle">—</div>
                        <div class="track-artist" id="trackArtist">—</div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="progress-wrap mb-3">
                        <span class="time-label" id="timeCurrent">0:00</span>
                        <div class="progress-bar-track" id="progressTrack" onclick="seekTo(event)">
                            <div class="progress-bar-fill" id="progressFill" style="width:0%"></div>
                            <div class="progress-thumb" id="progressThumb" style="left:0%"></div>
                        </div>
                        <span class="time-label" id="timeDuration">0:00</span>
                    </div>

                    {{-- Controls --}}
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                        <button class="ctrl-btn" id="prevBtn" onclick="playPrev()" title="Anterior">
                            <i class="bi bi-skip-start-fill"></i>
                        </button>
                        <button class="ctrl-btn ctrl-play" id="playPauseBtn" onclick="togglePlay()" disabled title="Play / Pausa">
                            <i class="bi bi-play-fill" id="playIcon"></i>
                        </button>
                        <button class="ctrl-btn" id="nextBtn" onclick="playNext()" title="Siguiente">
                            <i class="bi bi-skip-end-fill"></i>
                        </button>
                    </div>

                    {{-- Volume --}}
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <button class="vol-mute" id="muteBtn" onclick="toggleMute()" title="Silenciar">
                            <i class="bi bi-volume-up-fill" id="volIcon"></i>
                        </button>
                        <input type="range" id="volumeSlider" class="vol-slider" min="0" max="100" value="80"
                            oninput="setVolume(this.value)">
                    </div>

                </div>{{-- /player-card --}}
            </div>{{-- /col izquierda --}}

            {{-- Columna derecha: Playlist --}}
            <div class="col-lg-8 col-md-7">
                <div class="playlist-container">
                    <div class="playlist-header-large">Lista de reproducción</div>
                    <div class="playlist-inner-large" id="playlistInner"></div>
                </div>
            </div>{{-- /col derecha --}}
        </div>{{-- /row --}}

    </div>{{-- /container-fluid --}}
</div>

{{-- ── Hidden audio element ────────────────────────────── --}}
<audio id="radioAudio"></audio>

<style>
.radio-page { min-height: 80vh; }

@keyframes pulse-live { 0%,100%{opacity:1} 50%{opacity:0.4} }

.filter-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #888;
    margin-bottom: 4px;
}
.filter-select {
    background: var(--pulse-surface, #1a1a1a);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 0.9rem;
    min-width: 200px;
    outline: none;
    cursor: pointer;
}
.filter-select:focus { border-color: var(--pulse-primary); }
.filter-select:disabled { opacity: 0.5; cursor: not-allowed; }

.player-card {
    background: var(--pulse-surface, #1a1a1a);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 24px;
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    margin-bottom: 3rem;
}

.player-art {
    width: 180px;
    height: 180px;
    margin: 0 auto 1.5rem;
    position: relative;
}
.art-idle {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    border: 2px dashed rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}
.vinyl {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: conic-gradient(from 0deg, #1a1a1a, #333, #1a1a1a, #2a2a2a, #1a1a1a);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 3px rgba(239,225,181,0.2), 0 8px 32px rgba(0,0,0,0.6);
    animation: spin 4s linear infinite paused;
}
.vinyl.spinning { animation-play-state: running; }
.vinyl-center {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--pulse-primary,#efe1b5) 30%, #121212 31%);
    box-shadow: 0 0 10px rgba(239,225,181,0.3);
}
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

.track-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--pulse-primary, #efe1b5);
    margin-bottom: 4px;
}
.track-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 2px;
}
.track-artist {
    font-size: 0.85rem;
    color: #888;
}

.progress-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}
.time-label { font-size: 0.72rem; color: #666; min-width: 34px; }
.progress-bar-track {
    flex: 1;
    height: 4px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    position: relative;
    cursor: pointer;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--pulse-primary, #efe1b5), #c9a84c);
    border-radius: 4px;
    pointer-events: none;
}
.progress-thumb {
    width: 12px; height: 12px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
    box-shadow: 0 0 6px rgba(239,225,181,0.6);
    transition: width 0.1s, height 0.1s;
}
.progress-bar-track:hover .progress-thumb { width: 16px; height: 16px; }

.ctrl-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    color: #ccc;
    width: 48px; height: 48px;
    border-radius: 50%;
    font-size: 1.2rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.ctrl-btn:hover { color: #fff; background: rgba(255,255,255,0.12); }
.ctrl-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.ctrl-play {
    width: 64px; height: 64px;
    font-size: 1.6rem;
    background: var(--pulse-primary, #efe1b5);
    color: #121212;
    border-color: transparent;
    box-shadow: 0 4px 20px rgba(239,225,181,0.25);
}
.ctrl-play:hover { background: #f5e9c5; color: #000; transform: scale(1.05); }
.ctrl-play:disabled { background: rgba(255,255,255,0.1); color: #555; box-shadow: none; }

.vol-mute {
    background: transparent;
    border: none;
    color: #888;
    font-size: 1rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.vol-mute:hover { color: #fff; }
.vol-slider {
    -webkit-appearance: none;
    width: 120px; height: 4px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    outline: none;
    cursor: pointer;
}
.vol-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--pulse-primary, #efe1b5);
    cursor: pointer;
}

.playlist-container {
    background: var(--pulse-surface, #1a1a1a);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 24px;
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    height: 100%;
}
.playlist-header-large { 
    font-size: 1.1rem; 
    font-weight: 700; 
    letter-spacing: 1px; 
    text-transform: uppercase; 
    color: var(--pulse-primary, #efe1b5);
    margin-bottom: 1.5rem;
    text-align: center;
}
.playlist-inner-large { 
    max-height: 600px; 
    overflow-y: auto;
    padding-right: 10px;
}
.playlist-inner-large::-webkit-scrollbar { width: 6px; }
.playlist-inner-large::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 3px; }
.playlist-inner-large::-webkit-scrollbar-thumb { background: var(--pulse-primary, #efe1b5); border-radius: 3px; }
.pl-item {
    display: flex; align-items: center; gap: 15px;
    padding: 15px 20px; border-radius: 12px; cursor: pointer;
    transition: background 0.15s;
    font-size: 1rem;
    margin-bottom: 8px;
    border: 1px solid rgba(255,255,255,0.05);
}
.pl-item:hover { 
    background: rgba(255,255,255,0.08); 
    border-color: rgba(239,225,181,0.2);
    transform: translateY(-1px);
}
.pl-item.active { 
    background: rgba(239,225,181,0.12); 
    border-color: var(--pulse-primary, #efe1b5);
}
.pl-item .pl-num { 
    width: 30px; 
    color: #666; 
    font-size: 0.9rem; 
    text-align: center; 
    font-weight: 600;
}
.pl-item.active .pl-num { 
    color: var(--pulse-primary); 
}
.pl-item .pl-title { 
    color: #fff; 
    flex: 1; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    font-size: 1.1rem;
    font-weight: 500;
}
.pl-item.active .pl-title { 
    color: #fff; 
    font-weight: 700; 
}
.pl-item .pl-artist { 
    color: var(--pulse-primary, #efe1b5); 
    font-size: 1rem;
    font-weight: 600;
    min-width: 150px;
    text-align: right;
}
</style>

<script>
const communitiesMap = @json($communities);

const audio          = document.getElementById('radioAudio');
const playPauseBtn   = document.getElementById('playPauseBtn');
const playIcon       = document.getElementById('playIcon');
const progressFill   = document.getElementById('progressFill');
const progressThumb  = document.getElementById('progressThumb');
const timeCurrent    = document.getElementById('timeCurrent');
const timeDuration   = document.getElementById('timeDuration');
const trackLabel     = document.getElementById('trackLabel');
const trackTitle     = document.getElementById('trackTitle');
const trackArtist    = document.getElementById('trackArtist');
const vinyl          = document.getElementById('vinyl');
const artIdle        = document.getElementById('artIdle');
const artPlaying     = document.getElementById('artPlaying');
const playlistWrap   = document.getElementById('playlistWrap');
const playlistInner  = document.getElementById('playlistInner');
const volIcon        = document.getElementById('volIcon');
const volSlider      = document.getElementById('volumeSlider');

let playlist = [];
let currentIndex = 0;
let currentCommunity = '';

// ── Cascading selectors ──────────────────────
function onCommunityChange(community) {
    currentCommunity = community;
    const pSel = document.getElementById('provinceSelect');
    pSel.innerHTML = '<option value="">— Todas —</option>';
    if (community && communitiesMap[community]) {
        communitiesMap[community].forEach(p => {
            const o = document.createElement('option');
            o.value = p; o.textContent = p;
            pSel.appendChild(o);
        });
        pSel.disabled = false;
    } else {
        pSel.disabled = true;
    }
    if (community) fetchSongs(community, '');
    else setIdle('Selecciona una comunidad');
}

function onProvinceChange(province) {
    fetchSongs(currentCommunity, province);
}

// ── Fetch playlist ───────────────────────────
function fetchSongs(community, province) {
    setIdle('Cargando…');
    playPauseBtn.disabled = true;
    resetUI();

    let url = `/radio/community/${encodeURIComponent(community)}`;
    if (province) url += `?province=${encodeURIComponent(province)}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            playlist = data;
            if (playlist.length > 0) {
                trackLabel.textContent = `${province || community} · ${playlist.length} pistas`;
                trackTitle.textContent  = '▶ Listo para reproducir';
                trackArtist.textContent = 'Pulsa play para comenzar';
                playPauseBtn.disabled = false;
                renderPlaylist();
                // La playlist ahora siempre es visible en la columna derecha
            } else {
                setIdle(`Sin canciones en ${province || community}`);
            }
        });
}

// ── Set idle state ───────────────────────────
function setIdle(msg) {
    trackLabel.textContent  = msg || 'Radio Pulse';
    trackTitle.textContent  = '—';
    trackArtist.textContent = '—';
    timeCurrent.textContent = '0:00';
    timeDuration.textContent= '0:00';
    progressFill.style.width = '0%';
    progressThumb.style.left = '0%';
    artIdle.classList.remove('d-none');
    artPlaying.classList.add('d-none');
    vinyl.classList.remove('spinning');
    playIcon.className = 'bi bi-play-fill';
    // Limpiar playlist pero mantener visible
}

function resetUI() {
    audio.pause();
    audio.src = '';
    playlist  = [];
    setIdle('');
}

// ── Playback ─────────────────────────────────
function playTrack(index) {
    if (index < 0 || index >= playlist.length) return;
    currentIndex = index;
    const track  = playlist[index];

    audio.src = '/storage/' + track.file_path;
    audio.volume = volSlider.value / 100;
    audio.play();

    trackLabel.textContent  = 'REPRODUCIENDO';
    trackTitle.textContent  = track.title;
    trackArtist.textContent = track.musician_profile?.stage_name ?? '';

    artIdle.classList.add('d-none');
    artPlaying.classList.remove('d-none');
    vinyl.classList.add('spinning');
    playIcon.className = 'bi bi-pause-fill';
    renderPlaylist();

    // Notify server of play
    fetch(`/songs/${track.id}/play`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
}

function togglePlay() {
    if (!playlist.length) return;
    if (audio.paused) {
        if (!audio.src || audio.src === window.location.href) {
            playTrack(currentIndex);
        } else {
            audio.play();
        }
        vinyl.classList.add('spinning');
        playIcon.className = 'bi bi-pause-fill';
    } else {
        audio.pause();
        vinyl.classList.remove('spinning');
        playIcon.className = 'bi bi-play-fill';
    }
}

function playNext() {
    if (!playlist.length) return;
    playTrack((currentIndex + 1) % playlist.length);
}

function playPrev() {
    if (!playlist.length) return;
    if (audio.currentTime > 3) {
        audio.currentTime = 0;
    } else {
        playTrack((currentIndex - 1 + playlist.length) % playlist.length);
    }
}

audio.addEventListener('ended', playNext);

// ── Progress ─────────────────────────────────
audio.addEventListener('timeupdate', () => {
    if (!audio.duration) return;
    const pct = (audio.currentTime / audio.duration) * 100;
    progressFill.style.width = pct + '%';
    progressThumb.style.left = pct + '%';
    timeCurrent.textContent  = fmtTime(audio.currentTime);
    timeDuration.textContent = fmtTime(audio.duration);
});

function seekTo(e) {
    const rect = e.currentTarget.getBoundingClientRect();
    const pct  = (e.clientX - rect.left) / rect.width;
    if (audio.duration) audio.currentTime = pct * audio.duration;
}

function fmtTime(s) {
    if (!s || isNaN(s)) return '0:00';
    const m = Math.floor(s / 60);
    const sec = String(Math.floor(s % 60)).padStart(2, '0');
    return `${m}:${sec}`;
}

// ── Volume ───────────────────────────────────
function setVolume(v) {
    audio.volume = v / 100;
    audio.muted  = (v == 0);
    updateVolIcon(v);
}

function toggleMute() {
    audio.muted = !audio.muted;
    updateVolIcon(audio.muted ? 0 : volSlider.value);
}

function updateVolIcon(v) {
    if (v == 0 || audio.muted) volIcon.className = 'bi bi-volume-mute-fill';
    else if (v < 50)           volIcon.className = 'bi bi-volume-down-fill';
    else                       volIcon.className = 'bi bi-volume-up-fill';
}

// Set initial volume
audio.volume = 0.8;

// ── Playlist render ──────────────────────────
function renderPlaylist() {
    playlistInner.innerHTML = '';
    playlist.forEach((t, i) => {
        const div = document.createElement('div');
        div.className = 'pl-item' + (i === currentIndex ? ' active' : '');
        div.innerHTML = `
            <span class="pl-num">${i === currentIndex ? '<i class="bi bi-equalizer-fill" style="color:var(--pulse-primary)"></i>' : (i+1)}</span>
            <span class="pl-title">${t.title}</span>
            <span class="pl-artist">${t.musician_profile?.stage_name ?? ''}</span>
        `;
        div.onclick = () => playTrack(i);
        playlistInner.appendChild(div);
    });
    // Scroll active into view
    const active = playlistInner.querySelector('.active');
    if (active) active.scrollIntoView({ block: 'nearest' });
}
</script>
</x-app-layout>