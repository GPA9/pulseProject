<section>
    <header>
        <p class="text-muted small mb-4">
            Actualiza la información de tu cuenta y correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label text-muted">Nombre</label>
            <input id="name" name="name" type="text" class="form-control bg-black border-secondary text-light"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <span class="text-danger small mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label text-muted">Correo Electrónico</label>
            <input id="email" name="email" type="email" class="form-control bg-black border-secondary text-light"
                value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <span class="text-danger small mt-1">{{ $message }}</span>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-warning">
                        Tu dirección de correo no está verificada.
                        <button form="send-verification" class="btn btn-link p-0 text-primary">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-success small">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">Guardar</button>

            @if (session('status') === 'profile-updated')
                <p class="text-success small mb-0 fade-in-out">Guardado.</p>
            @endif
        </div>
    </form>
</section>