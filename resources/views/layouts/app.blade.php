<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            padding: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .main-content {
            padding: 2rem 0;
            min-height: calc(100vh - 80px);
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        
        .btn {
            border-radius: 0.75rem;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .stats-card .icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        
        .stats-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }
        
        .stats-card .label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .table {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        
        .table tbody td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .badge {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .alert {
            border: none;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 0.75rem;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            margin-bottom: 2rem;
            border-radius: 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding-left: 1rem; padding-right: 1rem;">
        <!-- <div class="container"> -->
            <a class="navbar-brand" href="{{ route('welcome') }}">
                🍱 {{ config('app.name', 'Food Waste Management') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <!-- Navigation menu for authenticated users -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-chart-line me-2"></i>{{ __('messages.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('restaurant.*') ? 'active' : '' }}" href="{{ route('restaurant.index') }}">
                                <i class="fas fa-store me-2"></i>{{ __('messages.restaurant') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}" href="{{ route('menus.index') }}">
                                <i class="fas fa-utensils me-2"></i>{{ __('messages.menu') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('food-item.*') ? 'active' : '' }}" href="{{ route('food-item.index') }}">
                                <i class="fas fa-hamburger me-2"></i>{{ __('messages.food_items') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                <i class="fas fa-receipt me-2"></i>{{ __('messages.Orders') }}
                            </a>
                        </li>

                    </ul>
                @else
                    <!-- Navigation menu for guests -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}" href="{{ route('welcome') }}">
                                <i class="fas fa-home me-2"></i>{{ __('messages.features') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#how-it-works">
                                <i class="fas fa-play me-2"></i>{{ __('messages.how_it_works') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">
                                <i class="fas fa-envelope me-2"></i>{{ __('messages.contact') }}
                            </a>
                        </li>
                    </ul>
                @endauth
                
                <!-- Language Switcher -->
                <ul class="navbar-nav me-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i>
                            @if(app()->getLocale() == 'ja')
                                🇯🇵 日本語
                            @else
                                🇻🇳 Tiếng Việt
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'ja') }}">🇯🇵 日本語</a></li>
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'vi') }}">🇻🇳 Tiếng Việt</a></li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Authentication Menu -->
                @auth
                    <!-- Profile Dropdown for logged in users -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('restaurant.index') }}">
                                        <i class="fas fa-store me-2"></i>{{ __('messages.restaurant') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-cog me-2"></i>{{ __('messages.settings') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <!-- Login/Register buttons for guests -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary me-2" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-2"></i>{{ __('messages.login') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-2"></i>{{ __('messages.register') }}
                            </a>
                        </li>
                    </ul>
                @endauth
            </div>
        <!-- </div> -->
    </nav>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <main class="main-content">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Add active class to current nav item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
