<x-app-layout>
    <div class="container py-5">
        {{-- Header --}}
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                 style="width:64px;height:64px;background:linear-gradient(135deg,var(--pulse-primary),#c9a84c);">
                <i class="bi bi-cloud-fill text-dark fs-3"></i>
            </div>
            <h2 class="fw-bold text-white mb-2">Almacenamiento en la Nube</h2>
            <p class="text-white-50" style="font-size:1.05rem;">Expande tu espacio y accede a funciones premium</p>
        </div>

        @if(session('success'))
            <div class="alert border-0 mb-4" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);border-radius:12px;">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert border-0 mb-4" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);border-radius:12px;">
                <i class="bi bi-x-circle me-2"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert border-0 mb-4" style="background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.3);border-radius:12px;">
                <i class="bi bi-info-circle me-2"></i> {{ session('info') }}
            </div>
        @endif

        {{-- Suscripción Actual --}}
        @if($currentSubscription)
            <div class="card border-0 mb-5" style="background:var(--pulse-surface);border-radius:16px;overflow:hidden;">
                <div style="height:4px;background:linear-gradient(90deg,#10b981,#059669);"></div>
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="fw-bold text-white mb-1">
                                <i class="bi bi-cloud-check-fill me-2" style="color:#10b981;"></i>
                                Plan {{ ucfirst($currentSubscription->plan_type) }}
                            </h5>
                            <span class="badge" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);font-size:0.85rem;">
                                {{ $currentSubscription->status === 'active' ? '● Activo' : ucfirst($currentSubscription->status) }}
                            </span>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50 small">Precio mensual</div>
                            <div class="fw-bold fs-4" style="color:var(--pulse-primary);">{{ number_format($currentSubscription->price, 2, ',', '.') }} €</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 text-center" style="background:rgba(239,225,181,0.08);border:1px solid rgba(239,225,181,0.2);">
                                <div class="text-white-50 mb-2" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Almacenamiento</div>
                                <div class="fw-bold fs-3 text-white">{{ $currentSubscription->storage_gb }} GB</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 text-center" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);">
                                <div class="text-white-50 mb-2" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Usado</div>
                                <div class="fw-bold fs-3" style="color:#34d399;">{{ number_format($currentSubscription->getStorageUsedPercentage(), 1) }}%</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 text-center" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);">
                                <div class="text-white-50 mb-2" style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;">Renovación</div>
                                <div class="fw-bold fs-4" style="color:#fbbf24;">
                                    {{ $currentSubscription->getDaysUntilExpiration() }} días
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($currentSubscription->isOnTrial())
                        <div class="alert border-0 mb-3" style="background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.3);border-radius:12px;">
                            <i class="bi bi-clock me-2"></i>
                            Estás en período de prueba. Tu suscripción se renovará automáticamente el {{ $currentSubscription->trial_ends_at->format('d/m/Y') }}.
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('subscriptions.destroy') }}" onsubmit="return confirm('¿Estás seguro de que quieres cancelar tu suscripción? Continuarás teniendo acceso hasta el final del período actual.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-1"></i>Cancelar Suscripción
                            </button>
                        </form>
                        <button class="btn btn-outline-light" onclick="upgradePlan()">
                            <i class="bi bi-arrow-up-circle me-1"></i>Cambiar Plan
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Planes Disponibles --}}
        <div class="row g-4 justify-content-center">
            @foreach($plans as $planType => $plan)
                @if(!$currentSubscription || $currentSubscription->plan_type !== $planType)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 overflow-hidden" 
                             style="background:var(--pulse-surface);border-radius:16px;border:1px solid {{ $planType === 'pro' ? 'var(--pulse-primary)' : 'rgba(255,255,255,0.08)' }};transition:transform 0.2s,box-shadow 0.2s;"
                             onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 48px rgba(0,0,0,0.4)'"
                             onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            @if($planType === 'pro')
                                <div class="text-center py-2" style="background:linear-gradient(90deg,var(--pulse-primary),#c9a84c);">
                                    <span class="fw-bold text-dark" style="font-size:0.8rem;letter-spacing:1px;">★ MÁS POPULAR ★</span>
                                </div>
                            @endif
                            
                            <div class="card-body p-4 p-lg-5">
                                {{-- Icono del plan --}}
                                <div class="text-center mb-4">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                                         style="width:56px;height:56px;background:{{ $planType === 'basic' ? 'rgba(59,130,246,0.15)' : ($planType === 'pro' ? 'rgba(239,225,181,0.15)' : 'rgba(168,85,247,0.15)') }};">
                                        @if($planType === 'basic')
                                            <i class="bi bi-cloud fs-3" style="color:#60a5fa;"></i>
                                        @elseif($planType === 'pro')
                                            <i class="bi bi-cloud-fill fs-3" style="color:var(--pulse-primary);"></i>
                                        @else
                                            <i class="bi bi-cloud-lightning-fill fs-3" style="color:#c084fc;"></i>
                                        @endif
                                    </div>
                                    <h4 class="fw-bold text-white mb-2">{{ $plan['name'] }}</h4>
                                    <div class="d-flex align-items-baseline justify-content-center gap-1">
                                        <span class="display-5 fw-bold" style="color:var(--pulse-primary);">{{ number_format($plan['price'], 2, ',', '.') }}</span>
                                        <span class="display-5 fw-bold" style="color:var(--pulse-primary);">€</span>
                                    </div>
                                    <div class="text-white-50">al mes</div>
                                </div>

                                {{-- Almacenamiento --}}
                                <div class="text-center p-3 rounded-3 mb-4" 
                                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                                    <div class="fw-bold fs-2 text-white">{{ $plan['storage_gb'] }}</div>
                                    <div class="text-white-50 small">GB de almacenamiento</div>
                                </div>

                                {{-- Características --}}
                                <ul class="list-unstyled mb-4">
                                    @foreach($plan['features'] as $feature)
                                        <li class="d-flex align-items-start mb-3">
                                            <i class="bi bi-check-circle-fill me-2 mt-1" style="color:#34d399;flex-shrink:0;"></i>
                                            <span class="text-white">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                {{-- Botón --}}
                                <button class="btn w-100 py-2 {{ $planType === 'pro' ? 'btn-primary' : 'btn-outline-light' }}"
                                        onclick="subscribe('{{ $planType }}')">
                                    @if($currentSubscription)
                                        <i class="bi bi-arrow-up-circle me-1"></i>Actualizar a {{ $plan['name'] }}
                                    @else
                                        <i class="bi bi-cloud-upload me-1"></i>Contratar {{ $plan['name'] }}
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Uso de Almacenamiento --}}
        @if($currentSubscription)
            <div class="card border-0 mt-5" style="background:var(--pulse-surface);border-radius:16px;">
                <div class="card-body p-4 p-lg-5">
                    <h5 class="fw-bold text-white mb-4">
                        <i class="bi bi-bar-chart me-2" style="color:var(--pulse-primary);"></i>Uso de Almacenamiento
                    </h5>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white">Usado: {{ number_format($currentSubscription->getStorageUsedGb(), 2) }} GB</span>
                            <span class="text-white-50">Total: {{ $currentSubscription->storage_gb }} GB</span>
                        </div>
                        <div class="progress" style="height:12px;background:rgba(255,255,255,0.08);border-radius:6px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width:{{ $currentSubscription->getStorageUsedPercentage() }}%;background:linear-gradient(90deg,var(--pulse-primary),#c9a84c);border-radius:6px;"
                                 aria-valuenow="{{ $currentSubscription->getStorageUsedPercentage() }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.15);">
                                <i class="bi bi-music-note-beamed d-block mb-1" style="color:#60a5fa;"></i>
                                <div class="text-white-50 small">Música</div>
                                <div class="fw-bold text-white">{{ number_format($currentSubscription->getStorageUsedPercentage() * 0.6, 1) }}%</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.15);">
                                <i class="bi bi-image d-block mb-1" style="color:#fbbf24;"></i>
                                <div class="text-white-50 small">Imágenes</div>
                                <div class="fw-bold text-white">{{ number_format($currentSubscription->getStorageUsedPercentage() * 0.3, 1) }}%</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-3" style="background:rgba(168,85,247,0.08);border:1px solid rgba(168,85,247,0.15);">
                                <i class="bi bi-file-earmark d-block mb-1" style="color:#c084fc;"></i>
                                <div class="text-white-50 small">Otros</div>
                                <div class="fw-bold text-white">{{ number_format($currentSubscription->getStorageUsedPercentage() * 0.1, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripeKey = '{{ config('services.stripe.key') }}';
        const stripe = stripeKey ? Stripe(stripeKey) : null;

        function subscribe(planType) {
            if (!stripe) {
                alert('No se ha configurado correctamente Stripe (falta STRIPE_KEY).');
                return;
            }

            fetch('{{ route('subscriptions.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    plan_type: planType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    return stripe.redirectToCheckout({ sessionId: data.id });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }

        function upgradePlan() {
            document.querySelector('.row.g-4.justify-content-center').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</x-app-layout>
