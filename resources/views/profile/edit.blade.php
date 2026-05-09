<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-4">Perfil de Usuario</h2>

                <!-- Update Profile Information -->
                <div class="card bg-dark border-secondary shadow-lg mb-4">
                    <div class="card-header border-secondary bg-transparent py-3">
                        <h5 class="mb-0 fw-bold">Información del Perfil</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card bg-dark border-secondary shadow-lg mb-4">
                    <div class="card-header border-secondary bg-transparent py-3">
                        <h5 class="mb-0 fw-bold">Actualizar Contraseña</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card bg-dark border-danger shadow-lg">
                    <div class="card-header border-danger bg-transparent py-3">
                        <h5 class="mb-0 fw-bold text-danger">Eliminar Cuenta</h5>
                    </div>
                    <div class="card-body p-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>