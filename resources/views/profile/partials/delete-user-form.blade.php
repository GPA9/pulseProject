<section>
    <header>
        <p class="text-muted small mb-4">
            Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente.
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        Eliminar Cuenta
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-light">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-danger">¿Estás seguro de que quieres eliminar tu cuenta?</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted">
                            Uno vez eliminada, no podrás recuperar tus datos. Por favor, introduce tu contraseña para
                            confirmar que quieres eliminar tu cuenta de forma permanente.
                        </p>

                        <div class="mt-3">
                            <label for="password" class="form-label visually-hidden">Contraseña</label>
                            <input id="password" name="password" type="password"
                                class="form-control bg-black border-secondary text-light" placeholder="Contraseña">
                            @error('password', 'userDeletion')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>