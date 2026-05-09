<x-app-layout>
    <div class="container-fluid py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1">Panel de Administración</h2>
                <p class="text-muted mb-0">Gestión de usuarios y suscripciones</p>
            </div>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success bg-success text-white border-0 mb-4">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 mb-4">
                <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Estadísticas Generales --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(239, 225, 181, 0.1);">
                                <i class="bi bi-people-fill fs-4" style="color: var(--pulse-primary);"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-15 text-success">+{{ $stats['new_musicians_this_month'] }} este mes</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $stats['total_musicians'] }}</h3>
                        <p class="text-muted mb-0">Músicos Totales</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1);">
                                <i class="bi bi-credit-card fs-4" style="color: #10b981;"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-15 text-success">Activas</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $stats['active_subscriptions'] }}</h3>
                        <p class="text-muted mb-0">Suscripciones</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1);">
                                <i class="bi bi-currency-euro fs-4" style="color: #f59e0b;"></i>
                            </div>
                            <span class="badge bg-warning bg-opacity-15 text-warning">/mes</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($stats['total_revenue'], 2, ',', '.') }} €</h3>
                        <p class="text-muted mb-0">Ingresos Mensuales</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1);">
                                <i class="bi bi-exclamation-triangle fs-4" style="color: #ef4444;"></i>
                            </div>
                            <span class="badge bg-danger bg-opacity-15 text-danger">{{ $expiringSoon->count() }}</span>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $expiringSoon->count() }}</h3>
                        <p class="text-muted mb-0">Expiran en 7 días</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Suscripciones por Plan --}}
            <div class="col-lg-4">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-pie-chart me-2"></i>Suscripciones por Plan
                        </h5>
                        
                        @foreach($subscriptionsByPlan as $plan)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-semibold text-white">{{ ucfirst($plan->plan_type) }}</div>
                                    <small class="text-muted">{{ $plan->count }} usuarios</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: var(--pulse-primary);">{{ number_format($plan->revenue, 2, ',', '.') }} €</div>
                                    <small class="text-muted">/mes</small>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 4px; background: rgba(255,255,255,0.1);">
                                <div class="progress-bar" style="width: {{ ($plan->count / $stats['active_subscriptions']) * 100 }}%; background: var(--pulse-primary);"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Últimas Suscripciones --}}
            <div class="col-lg-8">
                <div class="card border-0 h-100" style="background: var(--pulse-surface); border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-clock-history me-2"></i>Últimas Suscripciones
                            </h5>
                            <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-primary btn-sm">Ver todas</a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-dark table-borderless align-middle mb-0">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--pulse-border);">
                                        <th class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Músico</th>
                                        <th class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Plan</th>
                                        <th class="text-muted fw-bold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Estado</th>
                                        <th class="text-muted fw-bold text-end" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px;">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSubscriptions as $subscription)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($subscription->musicianProfile->image_path)
                                                        <img src="{{ asset('images/band-logos/' . $subscription->musicianProfile->image_path) }}" 
                                                             class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;" alt="">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="bi bi-person-fill fs-6 text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold text-white">{{ $subscription->musicianProfile->stage_name }}</div>
                                                        <small class="text-muted">{{ $subscription->musicianProfile->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: rgba(239, 225, 181, 0.1); color: var(--pulse-primary); border: 1px solid rgba(239, 225, 181, 0.2);">
                                                    {{ ucfirst($subscription->plan_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($subscription->status === 'active')
                                                    <span class="badge bg-success bg-opacity-15 text-success">Activa</span>
                                                @elseif($subscription->status === 'canceled')
                                                    <span class="badge bg-danger bg-opacity-15 text-danger">Cancelada</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-15 text-warning">{{ ucfirst($subscription->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-muted">{{ $subscription->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas de Expiración --}}
        @if($expiringSoon->isNotEmpty())
            <div class="card border-0 mt-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Suscripciones que Expiran Pronto
                    </h5>
                    <div class="row g-3">
                        @foreach($expiringSoon->take(4) as $subscription)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(239, 68, 68, 0.05);">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.1);">
                                        <i class="bi bi-clock text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-white">{{ $subscription->musicianProfile->stage_name }}</div>
                                        <small class="text-danger">Expira en {{ $subscription->getDaysUntilExpiration() }} días</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning bg-opacity-15 text-warning">{{ ucfirst($subscription->plan_type) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Actualizar estadísticas en tiempo real cada 30 segundos
        setInterval(() => {
            fetch('{{ route('admin.api.stats') }}')
                .then(response => response.json())
                .then(data => {
                    // Actualizar las estadísticas en la página
                    document.querySelector('.card-body h3').textContent = data.musicians;
                    // Actualizar otras estadísticas según sea necesario
                })
                .catch(error => console.error('Error updating stats:', error));
        }, 30000);
    </script>
</x-app-layout>
