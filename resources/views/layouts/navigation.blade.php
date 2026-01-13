<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-mortarboard-fill"></i> SIAKAD
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}" 
                       href="{{ route('mahasiswa.index') }}">
                        <i class="bi bi-people"></i> Mahasiswa
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dosen.*') ? 'active' : '' }}" 
                       href="{{ route('dosen.index') }}">
                        <i class="bi bi-person-badge"></i> Dosen
                    </a>
                </li>

                {{-- COMMENT DULU MASTER DATA --}}
                {{-- 
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-building"></i> Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('fakultas.index') }}">
                                <i class="bi bi-bank"></i> Fakultas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('prodi.index') }}">
                                <i class="bi bi-journal-code"></i> Program Studi
                            </a>
                        </li>
                    </ul>
                </li>
                --}}
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear"></i> Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>