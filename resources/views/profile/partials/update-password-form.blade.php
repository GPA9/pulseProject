<section>
    <header>
        <p class="text-muted small mb-4">
            Asegúrate de que tu cuenta esté protegida usando una contraseña larga y aleatoria.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label text-muted">Contraseña Actual</label>
            <input id="current_password" name="current_password" type="password"
                class="form-control bg-black border-secondary text-light" autocomplete="current-password">
            @error('current_password')
                <span class="text-danger small mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label text-muted">Nueva Contraseña</label>
            <input id="update_password_password" name="password" type="password"
                class="form-control bg-black border-secondary text-light" autocomplete="new-password">
            @error('password')
                <span class="text-danger small mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label text-muted">Confirmar
                Contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control bg-black border-secondary text-light" autocomplete="new-password">
            @error('password_confirmation')
                <span class="text-danger small mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">Guardar</button>

            @if (session('status') === 'password-updated')
                <p class="text-success small mb-0">Guardado.</p>
            @endif
        </div>
    </form>
</section>