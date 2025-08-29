<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Bidding Opportunities - JetLogue Travels</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
    :root {
        --jetlogue-primary: #1e3a8a;
        --jetlogue-secondary: #3b82f6;
        --jetlogue-accent: #fbbf24;
        --jetlogue-light: #dbeafe;
        --jetlogue-yellow-light: #fef3c7;
        --jetlogue-dark: #0f172a;
        --jetlogue-gray: #64748b;
        --gradient-primary: linear-gradient(135deg, var(--jetlogue-primary) 0%, var(--jetlogue-secondary) 100%);
        --gradient-accent: linear-gradient(135deg, var(--jetlogue-accent) 0%, #f59e0b 100%);
        --card-shadow: 0 10px 30px rgba(30, 58, 138, 0.08);
        --hover-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
        --transition-smooth: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        color: var(--jetlogue-dark);
        line-height: 1.7;
        overflow-x: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, var(--jetlogue-light) 100%);
    }
    
    .hero-section {
        background: var(--gradient-primary);
        background-attachment: fixed;
        color: white;
        padding: 200px 0 180px;
        margin-bottom: 80px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(30, 58, 138, 0.2);
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 120px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23f8fafc' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-position: center;
    }
    
    .hero-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-section .btn {
        transition: all 0.3s ease;
        transform: translateY(0);
    }
    
    .hero-section .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
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
        opacity: 0.1;
        border-radius: 50%;
        background: white;
        animation: float 15s infinite linear;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(5deg);
        }
    }
    
    .bid-card {
        transition: var(--transition-smooth);
        margin-bottom: 25px;
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        background: white;
        position: relative;
    }
    
    .bid-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: var(--gradient-primary);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    
    .bid-card:hover::before {
        transform: scaleX(1);
    }
    
    .bid-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }
    
    .bid-status {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-open {
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }
    
    .status-ended {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    
    .bid-budget {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--jetlogue-primary);
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .bid-meta {
        font-size: 0.875rem;
        color: var(--jetlogue-gray);
    }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto;
    }
    
    .cta-section {
        background: var(--gradient-primary);
        color: white;
        padding: 100px 0;
        margin-top: 100px;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20zm0 0c11.046 0 20 8.954 20 20H20V20z'/%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.3;
    }
    
    .navbar {
        transition: all 0.3s ease;
    }
    
    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }
    
    .navbar.scrolled .navbar-brand,
    .navbar.scrolled .nav-link {
        color: var(--jetlogue-dark) !important;
    }
    
    /* Logo Styling */
    .logo-container {
        position: relative;
    }
    
    .logo-image {
        height: 40px;
        width: auto;
        transition: all 0.3s ease;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
    }
    
    .navbar-brand:hover .logo-image {
        transform: scale(1.05);
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
    }
    
    .navbar.scrolled .logo-image {
        filter: drop-shadow(0 2px 8px rgba(30, 58, 138, 0.3));
    }
    
    /* Footer Logo */
    .footer-logo-image {
        height: 32px;
        width: auto;
        filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.1));
    }
</style>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <div class="logo-container me-2">
                <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="JetLogue Travels Logo" class="logo-image">
            </div>
            JetLogue Travels
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#bids">Bids</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
                @if($isLoggedIn)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm ms-2" href="#" id="vendorDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ $vendor->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('vendor.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('vendor.bids') }}"><i class="fas fa-file-alt me-2"></i>My Bids</a></li>
                            <li><a class="dropdown-item" href="{{ route('vendor.profile') }}"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('vendor.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2" href="{{ route('vendor.login') }}">Vendor Login</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section" id="home">
    <div class="floating-shapes">
        <div class="shape" style="width: 50px; height: 50px; top: 20%; left: 10%; animation-delay: 0s;"></div>
        <div class="shape" style="width: 30px; height: 30px; top: 60%; left: 80%; animation-delay: 2s;"></div>
        <div class="shape" style="width: 40px; height: 40px; top: 80%; left: 20%; animation-delay: 4s;"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">
                    Premium Travel Bidding Opportunities
                </h1>
                <p class="lead mb-5 animate__animated animate__fadeIn animate__delay-1s">
                    Partner with JetLogue Travels and access exclusive bidding opportunities in luxury travel, logistics, and hospitality services. Join our elite network of trusted vendors.
                </p>
                <div class="animate__animated animate__fadeInUp animate__delay-2s">
                    @if($isLoggedIn)
                        <a href="{{ route('vendor.dashboard') }}" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </a>
                        <a href="{{ route('vendor.bids') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-file-alt me-2"></i>My Bids
                        </a>
                    @else
                        <a href="{{ route('vendor.register') }}" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Register as Vendor
                        </a>
                        <a href="#bids" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-search me-2"></i>View Bids
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="hero-image-container text-center">
                    <div class="hero-icon-wrapper">
                        <i class="fas fa-plane-departure" style="font-size: 8rem; color: rgba(255,255,255,0.2);"></i>
                        <div class="floating-icons">
                            <i class="fas fa-handshake" style="position: absolute; top: 20%; left: 20%; font-size: 2rem; color: var(--jetlogue-accent); animation: float 3s ease-in-out infinite;"></i>
                            <i class="fas fa-award" style="position: absolute; top: 60%; right: 20%; font-size: 2rem; color: var(--jetlogue-accent); animation: float 3s ease-in-out infinite reverse;"></i>
                            <i class="fas fa-globe" style="position: absolute; bottom: 20%; left: 30%; font-size: 2rem; color: var(--jetlogue-accent); animation: float 3s ease-in-out infinite;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Active Bids Section -->
<section class="py-5" id="bids">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-4">Active Travel & Logistics Opportunities</h2>
                <p class="lead text-muted">Discover premium travel service opportunities and submit your competitive proposals</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($activeBids as $bid)
            <div class="col-lg-6 col-xl-4">
                <div class="bid-card">
                    <div class="card-body p-4">
                        <div class="bid-status {{ $bid['current_status'] === 'Open' ? 'status-open' : 'status-ended' }}">
                            {{ $bid['current_status'] }}
                        </div>
                        
                        <h5 class="card-title fw-bold mb-3">{{ $bid['title'] }}</h5>
                        <p class="text-muted mb-3">{{ $bid['category'] }}</p>
                        @if(!empty($bid['description']))
                        <p class="mb-3 text-secondary">{{ $bid['description'] }}</p>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Budget</small>
                                <div class="bid-budget">₱{{ number_format($bid['budget']) }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Submissions</small>
                                <div class="fw-bold">{{ $bid['submission_count'] }}</div>
                            </div>
                        </div>
                        
                        <div class="bid-meta mb-3">
                            <div><i class="fas fa-calendar me-2"></i>Start: {{ \Carbon\Carbon::parse($bid['start_date'])->format('M d, Y') }}</div>
                            <div><i class="fas fa-clock me-2"></i>End: {{ \Carbon\Carbon::parse($bid['end_date'])->format('M d, Y') }}</div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $bid['submission_count'] }} submissions</small>
                                    @if($bid['current_status'] === 'Open')
                                        <a href="{{ route('vendor.bid.form', $bid['id']) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-paper-plane me-1"></i>Submit Bid
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">{{ $bid['current_status'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light" id="features">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-4">Why Partner with JetLogue Travels?</h2>
                <p class="lead text-muted">Join our exclusive network and unlock premium benefits</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Premium Partnership</h4>
                    <p class="text-muted">Build long-term relationships with a leading luxury travel company.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Expand Your Reach</h4>
                    <p class="text-muted">Partner with JetLogue Travels to access high-value clients and premium travel opportunities.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Trusted Platform</h4>
                    <p class="text-muted">Our secure, industry-leading platform ensures fair competition and transparent processes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section text-center">
    <div class="container">
        <h2 class="fw-bold mb-4 animate__animated animate__fadeInDown">Ready to Elevate Your Business?</h2>
        <p class="lead mb-5 animate__animated animate__fadeIn animate__delay-1s">
            @if($isLoggedIn)
                Welcome back, {{ $vendor->name }}! Continue exploring opportunities and managing your bids.
            @else
                Join JetLogue Travels' exclusive vendor network and access premium travel opportunities worldwide
            @endif
        </p>
        <div class="animate__animated animate__fadeInUp animate__delay-2s">
            @if($isLoggedIn)
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-light btn-lg me-3">Go to Dashboard</a>
                <a href="{{ route('vendor.bids') }}" class="btn btn-outline-light btn-lg">My Bids</a>
            @else
                <a href="{{ route('vendor.register') }}" class="btn btn-light btn-lg me-3">Register Now</a>
                <a href="{{ route('vendor.login') }}" class="btn btn-outline-light btn-lg">Login</a>
            @endif
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="d-flex align-items-center">
                    <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="JetLogue Travels Logo" class="footer-logo-image me-2">
                    JetLogue Travels
                </h5>
                <p class="mb-0">Your premium partner in luxury travel and hospitality services.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">&copy; {{ date('Y') }} JetLogue Travels. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Animation on scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('.animate__animated');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementPosition < windowHeight - 100) {
                const animationClass = element.classList.contains('animate__fadeIn') ? 'animate__fadeIn' : 
                                     element.classList.contains('animate__fadeInUp') ? 'animate__fadeInUp' : 
                                     element.classList.contains('animate__fadeInDown') ? 'animate__fadeInDown' : '';
                
                if (animationClass) {
                    element.classList.add(animationClass);
                }
            }
        });
    }
    
    // Initial check
    animateOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', animateOnScroll);
});
</script>
</body>
</html>
