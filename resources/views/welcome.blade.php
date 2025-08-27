<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Waste Management System - 食品廃棄物管理システム</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary-hero {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
        }
        
        .btn-outline-hero {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-outline-hero:hover {
            background: white;
            color: #667eea;
        }
        
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: #2c3e50;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }
        
        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .feature-description {
            color: #6c757d;
            line-height: 1.6;
        }
        
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem 1rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .how-it-works {
            padding: 80px 0;
            background: white;
        }
        
        .step-card {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .step-card:hover {
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }
        
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta-description {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .footer-links {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #667eea !important;
        }
        
        .nav-link {
            font-weight: 500;
            color: #2c3e50 !important;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #667eea !important;
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                🍱 Food Waste Management
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">{{ __('messages.features') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">{{ __('messages.how_it_works') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">{{ __('messages.contact') }}</a>
                    </li>
                </ul>
                
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
                
                @auth
                    <!-- Profile Menu for authenticated users -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-chart-line me-2"></i>{{ __('messages.dashboard') }}
                                    </a>
                                </li>
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
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <!-- Login/Register buttons for guests -->
                    <ul class="navbar-nav">
                        <li class="nav-item me-2">
                            <a class="nav-link btn btn-outline-primary" href="{{ route('login') }}">{{ __('messages.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary" href="{{ route('register') }}">{{ __('messages.get_started') }}</a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">
                        {{ __('messages.hero_title') }}
                    </h1>
                    <p class="hero-subtitle">
                        {{ __('messages.hero_subtitle') }}
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('register') }}" class="btn-hero btn-primary-hero">
                            <i class="fas fa-rocket"></i>{{ __('messages.start_free_trial') }}
                        </a>
                        <a href="#features" class="btn-hero btn-outline-hero">
                            <i class="fas fa-play"></i>{{ __('messages.learn_more') }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image">
                        <i class="fas fa-chart-line" style="font-size: 15rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">{{ __('messages.why_choose') }}</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            🤖
                        </div>
                        <h3 class="feature-title">{{ __('messages.ai_insights_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.ai_insights_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            📊
                        </div>
                        <h3 class="feature-title">{{ __('messages.analytics_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.analytics_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            💰
                        </div>
                        <h3 class="feature-title">{{ __('messages.cost_reduction_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.cost_reduction_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            🌱
                        </div>
                        <h3 class="feature-title">{{ __('messages.sustainability_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.sustainability_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            📱
                        </div>
                        <h3 class="feature-title">{{ __('messages.easy_to_use_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.easy_to_use_desc') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            🔒
                        </div>
                        <h3 class="feature-title">{{ __('messages.secure_title') }}</h3>
                        <p class="feature-description">
                            {{ __('messages.secure_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">{{ __('messages.restaurants') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">30%</div>
                        <div class="stat-label">{{ __('messages.waste_reduction') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">¥2M+</div>
                        <div class="stat-label">{{ __('messages.cost_saved') }}</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">95%</div>
                        <div class="stat-label">{{ __('messages.satisfaction_rate') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2 class="section-title">{{ __('messages.how_it_works_title') }}</h2>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>{{ __('messages.step_1_title') }}</h4>
                        <p>{{ __('messages.step_1_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>{{ __('messages.step_2_title') }}</h4>
                        <p>{{ __('messages.step_2_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>{{ __('messages.step_3_title') }}</h4>
                        <p>{{ __('messages.step_3_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4>{{ __('messages.step_4_title') }}</h4>
                        <p>{{ __('messages.step_4_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">{{ __('messages.ready_to_transform') }}</h2>
            <p class="cta-description">
                {{ __('messages.cta_description') }}
            </p>
            <div class="hero-buttons justify-content-center">
                @auth
                    <!-- Buttons for authenticated users -->
                    <a href="{{ route('dashboard') }}" class="btn-hero btn-primary-hero">
                        <i class="fas fa-chart-line"></i>{{ __('messages.go_to_dashboard') }}
                    </a>
                    <a href="{{ route('restaurant.index') }}" class="btn-hero btn-outline-hero">
                        <i class="fas fa-store"></i>{{ __('messages.manage_restaurants') }}
                    </a>
                @else
                    <!-- Buttons for guests -->
                    <a href="{{ route('register') }}" class="btn-hero btn-outline-hero">
                        <i class="fas fa-rocket"></i>{{ __('messages.start_free_trial') }}
                    </a>
                    <a href="{{ route('login') }}" class="btn-hero btn-primary-hero">
                        <i class="fas fa-sign-in-alt"></i>{{ __('messages.login_to_account') }}
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="features-section">
        <div class="container">
            <h2 class="section-title">{{ __('messages.get_in_touch') }}</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <h4 class="mb-3">
                                        <i class="fas fa-envelope text-primary me-2"></i>{{ __('messages.contact_info') }}
                                    </h4>
                                    <div class="mb-3">
                                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                                        <strong>{{ __('messages.address') }}:</strong><br>
                                        Tokyo, Japan
                                    </div>
                                    <div class="mb-3">
                                        <i class="fas fa-phone text-info me-2"></i>
                                        <strong>{{ __('messages.phone') }}:</strong><br>
                                        +81-3-XXXX-XXXX
                                    </div>
                                    <div class="mb-3">
                                        <i class="fas fa-envelope text-warning me-2"></i>
                                        <strong>{{ __('messages.email') }}:</strong><br>
                                        info@foodwaste.com
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="mb-3">
                                        <i class="fas fa-clock text-primary me-2"></i>{{ __('messages.business_hours') }}
                                    </h4>
                                    <div class="mb-3">
                                        <strong>{{ __('messages.monday_friday') }}:</strong><br>
                                        9:00 AM - 6:00 PM JST
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('messages.saturday') }}:</strong><br>
                                        10:00 AM - 4:00 PM JST
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('messages.sunday') }}:</strong><br>
                                        {{ __('messages.closed') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-chart-line me-2"></i>{{ __('messages.access_dashboard') }}
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-rocket me-2"></i>{{ __('messages.start_your_free_trial') }}
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h5>🍱 Food Waste Management</h5>
                    <p class="mb-0">{{ __('messages.smart_solutions') }}</p>
                </div>
                <div class="footer-links">
                    <a href="#features">{{ __('messages.features') }}</a>
                    <a href="#how-it-works">{{ __('messages.how_it_works') }}</a>
                    <a href="#contact">{{ __('messages.contact') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a>
                        <a href="{{ route('restaurant.index') }}">{{ __('messages.restaurant') }}</a>
                    @else
                        <a href="{{ route('login') }}">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}">{{ __('messages.register') }}</a>
                    @endauth
                </div>
            </div>
            <hr class="my-4" style="border-color: #34495e;">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Food Waste Management System. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe feature cards and step cards
        document.querySelectorAll('.feature-card, .step-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Add hover effects to buttons
        document.querySelectorAll('.btn-hero').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.05)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add counter animation for stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target + (element.textContent.includes('+') ? '+' : '') + (element.textContent.includes('M') ? 'M' : '') + (element.textContent.includes('%') ? '%' : '');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start) + (element.textContent.includes('+') ? '+' : '') + (element.textContent.includes('M') ? 'M' : '') + (element.textContent.includes('%') ? '%' : '');
                }
            }, 16);
        }

        // Animate stats when they come into view
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    const text = statNumber.textContent;
                    const number = parseInt(text.replace(/[^0-9]/g, ''));
                    
                    if (number) {
                        statNumber.textContent = '0';
                        setTimeout(() => {
                            animateCounter(statNumber, number);
                        }, 500);
                    }
                    
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat-item').forEach(stat => {
            statsObserver.observe(stat);
        });

        // Add parallax effect to hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            if (heroSection) {
                heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>
</body>
</html>
