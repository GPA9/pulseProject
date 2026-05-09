<x-app-layout>
    <div class="container py-5 text-center" style="max-width: 480px;">

        <div class="mb-4 mx-auto d-flex align-items-center justify-content-center rounded-circle"
            style="width: 96px; height: 96px; background: rgba(239,68,68,0.12); border: 2px solid rgba(239,68,68,0.3);">
            <i class="bi bi-x-lg" style="font-size: 3rem; color: #ef4444;"></i>
        </div>

        <h1 class="fw-bold mb-2" style="font-size:1.8rem;">Pago cancelado</h1>
        <p class="text-muted mb-5">
            No se ha realizado ningún cargo. Puedes volver e intentarlo de nuevo cuando quieras.
        </p>

        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ route('concerts.index') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Volver a Conciertos
            </a>
            <a href="{{ route('merch.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                <i class="bi bi-bag me-2"></i>Ver Merchandising
            </a>
        </div>
    </div>
</x-app-layout>