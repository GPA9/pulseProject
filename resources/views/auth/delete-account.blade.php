<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark border-secondary shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logoPulse.png') }}" alt="Pulse Logo" height="60" class="mb-3">
                            <h3 class="fw-bold">Eliminar Cuenta</h3>
                            <p class="text-muted">¿Estás seguro de que quieres eliminar tu cuenta?</p>
                        </div>

                        <div class="alert alert-danger bg-danger text-white border-0 mb-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Advertencia:</strong> Esta acción es irreversible. Todos tus datos, música, merchandising y conciertos serán eliminados permanentemente.
                        </div>

                        <form method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            
                            <div class="mb-3">
                                <label for="password" class="form-label text-muted">Confirma tu contraseña para eliminar la cuenta</label>
                                <input id="password" type="password" name="password" 
                                       class="form-control bg-black border-secondary text-light" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-secondary" onclick="history.back()">
                                    <i class="bi bi-arrow-left me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Eliminar Cuenta Permanentemente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm('¿ESTÁS SEGURO DE QUE QUIERES ELIMINAR TU CUENTA?\n\nEsta acción NO se puede deshacer.\n\nSe eliminarán permanentemente:\n• Tu perfil de usuario\n• Tu perfil de músico (si aplica)\n• Todas tus canciones y álbumes\n• Todos tus productos de merchandising\n• Todos tus conciertos\n• Todas tus estadísticas y datos\n\n¿Deseas continuar?');
        }
    </script>
</x-app-layout>
