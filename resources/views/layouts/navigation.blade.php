<nav class="navbar-custom">
    <div class="container-fluid d-flex align-items-center justify-content-between px-4">

        <!-- Logo (left) -->
        <a class="navbar-brand flex-shrink-0" href="{{ route('home') }}">
            <img src="{{ asset('images/logoPulse.png') }}" alt="Pulse Logo">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler d-lg-none border-0 ms-auto me-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <!-- Main Links (centered) -->
        <div class="collapse navbar-collapse d-lg-flex justify-content-center flex-grow-1" id="mainNav">
            <ul
                class="navbar-nav d-flex flex-column flex-lg-row align-items-center gap-2 gap-lg-4 text-center w-100 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('musicians.index') ? 'active' : '' }}"
                        href="{{ route('musicians.index') }}">Artistas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('radio.index') ? 'active' : '' }}"
                        href="{{ route('radio.index') }}">Radio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('merch.index') ? 'active' : '' }}"
                        href="{{ route('merch.index') }}">Merchandising</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('concerts.index') ? 'active' : '' }}"
                        href="{{ route('concerts.index') }}">Conciertos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('top-musicians.index') ? 'active' : '' }}"
                        href="{{ route('top-musicians.index') }}">Top Artistas</a>
                </li>
            </ul>
        </div>

        <!-- User Widget (right) -->
        <div class="d-none d-lg-flex align-items-center gap-2 flex-shrink-0">
            @auth
                    {{-- Cart icon --}}
                    {{-- @php
                        $cartItems = session()->get('cart', []);
                        $cartCount = collect($cartItems)->sum('quantity');
                    @endphp
                    <a href="{{ Route::has('cart.index') ? route('cart.index') : '#' }}" class="position-relative d-flex align-items-center justify-content-center"
                        style="width:38px;height:38px;border-radius:50%;background:rgba(239,225,181,0.08);border:1px solid rgba(239,225,181,0.2);text-decoration:none;transition:background 0.2s;"
                        onmouseover="this.style.background='rgba(239,225,181,0.18)'"
                        onmouseout="this.style.background='rgba(239,225,181,0.08)'">
                        <i class="bi bi-cart3" style="font-size:1.1rem;color:var(--pulse-primary);"></i>
                        @if($cartCount > 0)
                            <span class="position-absolute badge rounded-pill"
                                style="background:var(--pulse-primary);color:#121212;font-size:0.6rem;font-weight:700;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;top:-4px;right:-4px;padding:0 4px;">
                                {{ $cartCount > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a> --}}

                    {{-- User dropdown --}}
                    <div class="dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-0" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $user = Auth::user();
                                $musicianProfile = $user->musicianProfile ?? null;
                            @endphp
                            
                            @if($musicianProfile && $musicianProfile->image_path)
                                @if(file_exists(public_path('images/band-logos/' . $musicianProfile->image_path)))
                                    <img src="{{ asset('images/band-logos/' . $musicianProfile->image_path) }}" 
                                @else
                                    <img src="{{ asset('storage/' . $musicianProfile->image_path) }}" 
                                @endif
                                    class="rounded-circle" 
                                    style="width: 35px; height: 35px; object-fit: cover;"
                                    alt="{{ $musicianProfile->stage_name }}"
                                    title="{{ $musicianProfile->stage_name }}">
                            @else
                                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white"
                                    style="width: 35px; height: 35px;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary"
                            aria-labelledby="navbarDropdown">
                            <li><span class="dropdown-header text-muted">{{ Auth::user()->name }}</span></li>
                            <li>
                                <a class="dropdown-item text-light hover-primary" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Panel de Control
                                </a>
                            </li>
                            @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item text-light hover-primary" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-lock me-2"></i>Admin
                                </a>
                            </li>
                            @endif
                            <li>
                                <a class="dropdown-item text-light hover-primary" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider bg-secondary"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-light hover-primary">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
            @else
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" href="{{ route('login') }}" style="min-width:70px;">Entrar</a>
                    <a class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" href="{{ route('register') }}" style="min-width:80px;">Registro</a>
                </div>
            @endauth
        </div>

    </div>

    <!-- Mobile user links inside collapse -->
    <div class="d-lg-none collapse navbar-collapse text-center py-2 border-top border-secondary" id="mainNav"
        style="display:none!important;">
    </div>
</nav>