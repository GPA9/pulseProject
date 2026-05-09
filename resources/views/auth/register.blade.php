<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logoPulse.png') }}" alt="Pulse Logo" height="60" class="mb-3">
                            <h3 class="fw-bold">Crear Cuenta</h3>
                            <p class="text-muted">Únete a la comunidad local</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label text-muted">Nombre</label>
                                <input id="name" class="form-control bg-black border-secondary text-light" type="text"
                                    name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                                @error('name')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted">Correo Electrónico</label>
                                <input id="email" class="form-control bg-black border-secondary text-light" type="email"
                                    name="email" value="{{ old('email') }}" required autocomplete="username">
                                @error('email')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label text-muted">Contraseña</label>
                                <input id="password" class="form-control bg-black border-secondary text-light"
                                    type="password" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label text-muted">Confirmar
                                    Contraseña</label>
                                <input id="password_confirmation"
                                    class="form-control bg-black border-secondary text-light" type="password"
                                    name="password_confirmation" required autocomplete="new-password">
                                @error('password_confirmation')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Tipo de cuenta</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="account_type" id="user_type" value="user" checked>
                                    <label class="btn btn-outline-secondary" for="user_type">
                                        <i class="bi bi-person me-1"></i>Usuario
                                    </label>
                                    <input type="radio" class="btn-check" name="account_type" id="musician_type" value="musician">
                                    <label class="btn btn-outline-secondary" for="musician_type">
                                        <i class="bi bi-music-note-beamed me-1"></i>Músico
                                    </label>
                                </div>
                            </div>

                            <!-- Musician Profile Fields (shown only when musician is selected) -->
                            <div id="musicianFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="stage_name" class="form-label text-muted">Nombre Artístico</label>
                                    <input id="stage_name" class="form-control bg-black border-secondary text-light" type="text"
                                        name="stage_name" value="{{ old('stage_name') }}" placeholder="Ej: Luna Barcelona">
                                    @error('stage_name')
                                        <span class="text-danger small mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="genre" class="form-label text-muted">Género Musical</label>
                                    <select id="genre" class="form-select bg-black border-secondary text-light" name="genre">
                                        <option value="">Selecciona tu género</option>
                                        <option value="Rock">Rock</option>
                                        <option value="Pop">Pop</option>
                                        <option value="Indie">Indie</option>
                                        <option value="Hip Hop">Hip Hop</option>
                                        <option value="Jazz">Jazz</option>
                                        <option value="Blues">Blues</option>
                                        <option value="Electronic">Electronic</option>
                                        <option value="Reggae">Reggae</option>
                                        <option value="Metal">Metal</option>
                                        <option value="Classical">Classical</option>
                                        <option value="R&B">R&B</option>
                                        <option value="Soul">Soul</option>
                                        <option value="Folk">Folk</option>
                                        <option value="Country">Country</option>
                                        <option value="Alternative">Alternative</option>
                                        <option value="Punk">Punk</option>
                                        <option value="Funk">Funk</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                    @error('genre')
                                        <span class="text-danger small mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label text-muted">Ciudad</label>
                                        <input id="city" class="form-control bg-black border-secondary text-light" type="text"
                                            name="city" value="{{ old('city') }}" placeholder="Ej: Barcelona">
                                        @error('city')
                                            <span class="text-danger small mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="province" class="form-label text-muted">Provincia</label>
                                        <input id="province" class="form-control bg-black border-secondary text-light" type="text"
                                            name="province" value="{{ old('province') }}" placeholder="Ej: Barcelona">
                                        @error('province')
                                            <span class="text-danger small mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="autonomous_community" class="form-label text-muted">Comunidad Autónoma</label>
                                    <select id="autonomous_community" class="form-select bg-black border-secondary text-light" name="autonomous_community">
                                        <option value="">Selecciona tu comunidad</option>
                                        <option value="Andalucía">Andalucía</option>
                                        <option value="Aragón">Aragón</option>
                                        <option value="Principado de Asturias">Principado de Asturias</option>
                                        <option value="Islas Baleares">Islas Baleares</option>
                                        <option value="Islas Canarias">Islas Canarias</option>
                                        <option value="Cantabria">Cantabria</option>
                                        <option value="Castilla-La Mancha">Castilla-La Mancha</option>
                                        <option value="Castilla y León">Castilla y León</option>
                                        <option value="Cataluña">Cataluña</option>
                                        <option value="Comunidad Valenciana">Comunidad Valenciana</option>
                                        <option value="Extremadura">Extremadura</option>
                                        <option value="Galicia">Galicia</option>
                                        <option value="La Rioja">La Rioja</option>
                                        <option value="Madrid">Madrid</option>
                                        <option value="Murcia">Murcia</option>
                                        <option value="Navarra">Navarra</option>
                                        <option value="País Vasco">País Vasco</option>
                                    </select>
                                    @error('autonomous_community')
                                        <span class="text-danger small mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label text-muted">Biografía</label>
                                    <textarea id="bio" class="form-control bg-black border-secondary text-light" name="bio" rows="3"
                                        placeholder="Cuéntanos sobre ti y tu música...">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <span class="text-danger small mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Registrarse
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <a class="text-muted small text-decoration-none" href="{{ route('login') }}">
                                    ¿Ya tienes cuenta? Inicia sesión
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Show/hide musician fields based on account type selection
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeRadio = document.getElementById('user_type');
        const musicianTypeRadio = document.getElementById('musician_type');
        const musicianFields = document.getElementById('musicianFields');

        function toggleMusicianFields() {
            if (musicianTypeRadio && musicianTypeRadio.checked) {
                musicianFields.style.display = 'block';
            } else {
                musicianFields.style.display = 'none';
            }
        }

        userTypeRadio.addEventListener('change', toggleMusicianFields);
        musicianTypeRadio.addEventListener('change', toggleMusicianFields);
    });
</script>