<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Gerenciador de Óticas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Navbar -->
        <nav class="app-header navbar navbar-expand-lg bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('categories.index') }}" class="brand-link">
                    <span class="brand-text fw-light">
                        <i class="bi bi-eyeglasses"></i> Óticas
                    </span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu" data-lte-toggle="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link @if(Route::is('categories.index')) active @endif">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Categorias</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.create') }}" class="nav-link @if(Route::is('categories.create')) active @endif">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Nova Categoria</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('page-title')</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading">
                                <i class="bi bi-exclamation-circle"></i> Erro!
                            </h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <script>
                            (function() {
                                const alert = document.getElementById('successAlert');
                                if (alert) {
                                    setTimeout(function() {
                                        alert.classList.remove('show');
                                        alert.addEventListener('transitionend', function() {
                                            alert.remove();
                                        }, { once: true });
                                    }, 5000);
                                }
                            })();
                        </script>
                    @endif

                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="app-footer border-top bg-body-tertiary">
                <div class="container-fluid py-2">
                    <div class="row">
                        <div class="col-md-6 text-muted">© {{ date('Y') }} Gerenciador de Óticas</div>
                        <div class="col-md-6 text-end small">v0.1</div>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <script>
        // Close sidebar when clicking outside on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.app-sidebar');
            const body = document.body;
            const mainContent = document.querySelector('.app-main');
            
            // Close sidebar when clicking on main content (only on mobile/when sidebar is overlay)
            if (mainContent) {
                mainContent.addEventListener('click', function(e) {
                    // Check if sidebar is in overlay mode (collapsed/mobile view)
                    if (body.classList.contains('sidebar-open') && window.innerWidth < 992) {
                        body.classList.remove('sidebar-open');
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
