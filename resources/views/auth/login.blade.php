<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logoPulse.png') }}" alt="Pulse Logo" height="60" class="mb-3">
                            <h3 class="fw-bold">Iniciar Sesión</h3>
                            <p class="text-muted">Bienvenido de nuevo a Pulse</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted">Correo Electrónico</label>
                                <input id="email" class="form-control bg-black border-secondary text-light" type="email"
                                    name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label text-muted">Contraseña</label>
                                <input id="password" class="form-control bg-black border-secondary text-light"
                                    type="password" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="text-danger small mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input id="remember_me" type="checkbox"
                                    class="form-check-input bg-black border-secondary" name="remember">
                                <label for="remember_me" class="form-check-label text-muted small">Recordarme</label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Entrar
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                @if (Route::has('password.request'))
                                    <a class="text-muted small text-decoration-none" href="{{ route('password.request') }}">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>