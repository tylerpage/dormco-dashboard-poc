<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
    
    <!-- PWA Meta Tags -->
    <meta name="application-name" content="DormCo Mgmt">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DormCo Mgmt">
    <meta name="description" content="DormCo Management Dashboard for managing orders, pallets, and schools">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#022350">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#022350">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered: ', registration);
                    })
                    .catch((registrationError) => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    
    <!-- PWA Install Instructions -->
    <script>
        // Check if app is already installed
        function isAppInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches || 
                   window.navigator.standalone === true;
        }
        
        // Show install instructions if not already installed
        function showInstallInstructions(forceShow = false) {
            // Don't show on authentication pages
            if (window.location.pathname.includes('/login') || 
                window.location.pathname.includes('/register') || 
                window.location.pathname.includes('/password/reset') ||
                window.location.pathname.includes('/email/verify')) {
                return;
            }
            
            if (!forceShow) {
                if (isAppInstalled()) return;
                if (localStorage.getItem('pwa-install-dismissed') === 'true') return;
            }
            
            // Create the popup
            const popup = document.createElement('div');
            popup.id = 'pwa-install-popup';
            popup.innerHTML = `
                <div class="pwa-install-overlay">
                    <div class="pwa-install-modal">
                        <div class="pwa-install-header">
                            <h4><i class="fas fa-mobile-alt"></i> Install DormCo Mgmt</h4>
                            <button type="button" class="btn-close" onclick="dismissInstallPopup()"></button>
                        </div>
                        <div class="pwa-install-body">
                            <p>Install this app on your device for a better experience:</p>
                            <div class="install-steps">
                                <div class="step">
                                    <strong>iPhone (Safari):</strong>
                                    <ol>
                                        <li>Tap the <i class="fas fa-share"></i> Share button at the bottom</li>
                                        <li>Scroll down and tap "Add to Home Screen"</li>
                                        <li>Tap "Add" to install</li>
                                    </ol>
                                </div>
                                <div class="step">
                                    <strong>Android (Chrome):</strong>
                                    <ol>
                                        <li>Tap the <i class="fas fa-plus"></i> menu button</li>
                                        <li>Select "Add to Home screen" or "Install app"</li>
                                        <li>Tap "Add" or "Install" to confirm</li>
                                    </ol>
                                </div>
                                <div class="step">
                                    <strong>Desktop (Chrome/Edge):</strong>
                                    <ol>
                                        <li>Look for the <i class="fas fa-download"></i> install icon in the address bar</li>
                                        <li>Click "Install" when prompted</li>
                                        <li>Or use the menu → "Install DormCo Mgmt"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="pwa-install-footer">
                            <button type="button" class="btn btn-secondary" onclick="dismissInstallPopup()">
                                Maybe Later
                            </button>
                            <button type="button" class="btn btn-primary" onclick="dismissInstallPopup()">
                                Got It!
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .pwa-install-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .pwa-install-modal {
                    background: white;
                    border-radius: 10px;
                    max-width: 500px;
                    width: 100%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                }
                .pwa-install-header {
                    padding: 20px 20px 0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .pwa-install-header h4 {
                    margin: 0;
                    color: #022350;
                }
                .pwa-install-body {
                    padding: 20px;
                }
                .install-steps {
                    margin-top: 15px;
                }
                .step {
                    margin-bottom: 20px;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    border-left: 4px solid #022350;
                }
                .step strong {
                    color: #022350;
                    display: block;
                    margin-bottom: 10px;
                }
                .step ol {
                    margin: 0;
                    padding-left: 20px;
                }
                .step li {
                    margin-bottom: 5px;
                }
                .pwa-install-footer {
                    padding: 0 20px 20px;
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                @media (max-width: 576px) {
                    .pwa-install-modal {
                        margin: 10px;
                        max-height: 90vh;
                    }
                    .pwa-install-header, .pwa-install-body, .pwa-install-footer {
                        padding: 15px;
                    }
                }
            `;
            
            document.head.appendChild(style);
            document.body.appendChild(popup);
        }
        
        // Dismiss the popup
        function dismissInstallPopup() {
            const popup = document.getElementById('pwa-install-popup');
            if (popup) {
                popup.remove();
            }
            // Remember that user dismissed it
            localStorage.setItem('pwa-install-dismissed', 'true');
        }
        
        // Show popup after a delay (only on first visit)
        window.addEventListener('load', () => {
            setTimeout(() => {
                showInstallInstructions();
            }, 3000); // Show after 3 seconds
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @auth
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" tabindex="-1" aria-labelledby="sidebarMenuLabel">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="{{ asset('dormco-dash-logo.png') }}" alt="DormCo" height="40">
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        
                        <!-- Pack and Hold Shipping Section -->
                        <li class="nav-item">
                            <span class="nav-link text-muted small text-uppercase fw-bold">Pack & Hold Shipping</span>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                <i class="fas fa-shopping-cart"></i> Orders
                            </a>
                        </li>
                        
                        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('pallets', auth()->user()->permissions ?? [])))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pallets.*') ? 'active' : '' }}" href="{{ route('pallets.index') }}">
                                <i class="fas fa-boxes"></i> Pallets
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('exports', auth()->user()->permissions ?? [])))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('exports.*') ? 'active' : '' }}" href="{{ route('exports.index') }}">
                                <i class="fas fa-download"></i> Exports
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('schools', auth()->user()->permissions ?? [])))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('schools.*') ? 'active' : '' }}" href="{{ route('schools.index') }}">
                                <i class="fas fa-school"></i> School Management
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <span class="nav-link text-muted small text-uppercase fw-bold">Administration</span>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        @endif
                    </ul>
                    
                    <!-- User Section -->
                    <div class="border-top mt-4 pt-3">
                        <div class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;" onclick="toggleUserDropdown(event)">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                                <i class="fas fa-chevron-down ms-2" style="font-size: 0.8em;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" id="userDropdown" aria-labelledby="dropdownUser1" style="position: absolute; right: 0; left: auto; display: none;">
                                <li><a class="dropdown-item" href="#" onclick="showInstallInstructions(true); return false;">
                                    <i class="fas fa-mobile-alt"></i> Add to Home Screen
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> Sign out
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </nav>
            @endauth
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-left: 0;">
                @auth
                <!-- Top navigation bar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <!-- Mobile menu button -->
                        <button class="btn btn-outline-secondary d-md-none me-3" type="button" onclick="toggleSidebar()">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="h2 mb-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('page-actions')
                    </div>
                </div>
                @endauth
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Enhancement -->
    <script>
        // Simple sidebar toggle for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            if (sidebar) {
                sidebar.classList.toggle('show');
            }
        }
        
        // User dropdown toggle
        function toggleUserDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebarMenu');
            const toggleBtn = event.target.closest('[onclick="toggleSidebar()"]');
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownToggle = event.target.closest('[onclick="toggleUserDropdown(event)"]');
            
            // Close user dropdown when clicking outside
            if (userDropdown && !userDropdown.contains(event.target) && !userDropdownToggle) {
                userDropdown.style.display = 'none';
            }
            
            if (window.innerWidth < 768 && sidebar && sidebar.classList.contains('show')) {
                if (!sidebar.contains(event.target) && !toggleBtn) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebarMenu');
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
