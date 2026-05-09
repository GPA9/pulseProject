<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-hero py-5 mb-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-3 fw-bold mb-3">
                        Siente el <span class="text-primary">Pulso</span> de<br>la Música Local.
                    </h1>
                    <p class="lead text-muted mb-4">
                        Descubre joyas ocultas en tu ciudad. Apoya a los artistas locales directamente.
                        Vive la música como nunca antes.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('musicians.index') }}" class="btn btn-primary btn-lg px-5">Empezar a
                            Escuchar</a>
                        <a href="#radio" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-broadcast me-2"></i> Radio Ciudad
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle bg-primary rounded-circle"
                            style="width: 300px; height: 300px; filter: blur(100px); opacity: 0.2; z-index: -1;"></div>
                        <img src="{{ asset('images/Publico.png') }}"
                            alt="Público" class="img-fluid rounded-4 shadow-lg" style="transform: rotate(-2deg); max-width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Radio Section -->
    <div class="container py-5" id="radio">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card bg-dark border border-secondary shadow-lg">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="bi bi-broadcast text-primary display-1"></i>
                        </div>
                        <h2 class="mb-2">Radio en Vivo</h2>
                        <p class="text-muted mb-4">Sintoniza lo que suena en tu zona ahora mismo.</p>

                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <select class="form-select form-select-lg w-auto bg-dark text-light border-secondary"
                                id="citySelect">
                                <option selected value="">Selecciona tu Ciudad</option>
                                <option value="Barcelona">Barcelona</option>
                                <option value="Madrid">Madrid</option>
                                <option value="Valencia">Valencia</option>
                            </select>

                            <button id="playBtn"
                                class="btn btn-primary rounded-circle p-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;" disabled>
                                <i class="bi bi-play-fill fs-2"></i>
                            </button>
                        </div>

                        <div class="mt-4 p-3 rounded bg-black bg-opacity-25 border border-secondary d-inline-block w-100"
                            style="max-width: 400px;">
                            <div id="playerStatus" class="d-flex flex-column align-items-center gap-2">
                                <span class="text-light small">Selecciona una ciudad para comenzar</span>
                            </div>
                            <audio id="radioAudio" class="w-100 mt-3 d-none" controls></audio>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-surface h-100">
                    <i class="bi bi-music-note-beamed text-primary fs-1 mb-3"></i>
                    <h4>Sube y Comparte</h4>
                    <p class="text-muted">Los músicos pueden subir pistas y llegar a una audiencia local al instante.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-surface h-100">
                    <i class="bi bi-ticket-perforated text-primary fs-1 mb-3"></i>
                    <h4>Vende Entradas</h4>
                    <p class="text-muted">Venta directa de entradas a fans para tus próximos conciertos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-4 bg-surface h-100">
                    <i class="bi bi-graph-up-arrow text-primary fs-1 mb-3"></i>
                    <h4>Sigue tu Crecimiento</h4>
                    <p class="text-muted">Estadísticas detalladas para ver cómo crece tu base de fans año tras año.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const citySelect = document.getElementById('citySelect');
            const playBtn = document.getElementById('playBtn');
            const audioPlayer = document.getElementById('radioAudio');
            const playerStatus = document.getElementById('playerStatus');

            let playlist = [];
            let currentTrackIndex = 0;

            citySelect.addEventListener('change', function () {
                if (this.value) {
                    playBtn.disabled = false;
                    fetchSongs(this.value);
                } else {
                    playBtn.disabled = true;
                }
            });

            playBtn.addEventListener('click', function () {
                if (playlist.length > 0) {
                    playTrack(0);
                    audioPlayer.classList.remove('d-none');
                    this.innerHTML = '<i class="bi bi-skip-forward-fill fs-2"></i>';
                    this.onclick = function () {
                        playNext();
                    };
                }
            });

            audioPlayer.addEventListener('ended', playNext);

            function fetchSongs(city) {
                playerStatus.innerHTML = '<div class="spinner-grow text-primary spinner-grow-sm" role="status"></div><span class="text-light small ms-2">Sintonizando ' + city + '...</span>';

                fetch(`/radio/${city}`)
                    .then(response => response.json())
                    .then(data => {
                        playlist = data;
                        if (playlist.length > 0) {
                            playerStatus.innerHTML = '<span class="text-success small"><i class="bi bi-wifi"></i> Conectado a ' + city + ' (' + playlist.length + ' pistas)</span>';
                        } else {
                            playerStatus.innerHTML = '<span class="text-warning small">No hay canciones en ' + city + '</span>';
                            playBtn.disabled = true;
                        }
                    });
            }

            function playTrack(index) {
                if (index < playlist.length) {
                    currentTrackIndex = index;
                    const track = playlist[index];
                    audioPlayer.src = '/storage/' + track.file_path;
                    audioPlayer.play();

                    playerStatus.innerHTML = `
                        <div class="text-start w-100">
                            <small class="text-primary d-block">REPRODUCIENDO AHORA</small>
                            <h6 class="text-white mb-0">${track.title}</h6>
                            <small class="text-muted">${track.musician_profile.stage_name}</small>
                        </div>
                    `;

                    // Record play
                    fetch(`/songs/${track.id}/play`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                }
            }

            function playNext() {
                currentTrackIndex++;
                if (currentTrackIndex >= playlist.length) {
                    currentTrackIndex = 0; // Loop
                }
                playTrack(currentTrackIndex);
            }
        });
    </script>
</x-app-layout>