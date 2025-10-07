<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
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
        function showInstallInstructions() {
            if (isAppInstalled()) return;
            
            // Check if user has dismissed the instructions before
            if (localStorage.getItem('pwa-install-dismissed') === 'true') return;
            
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
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('dormco-dash-logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    @auth
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item dropdown">
                            <a id="packHoldingDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Pack and Hold Shipping
                            </a>
                            <div class="dropdown-menu" aria-labelledby="packHoldingDropdown">
                                <a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a>
                                @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('pallets', auth()->user()->permissions ?? [])))
                                <a class="dropdown-item" href="{{ route('pallets.index') }}">Pallets</a>
                                @endif
                                @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('exports', auth()->user()->permissions ?? [])))
                                <a class="dropdown-item" href="{{ route('exports.index') }}">Exports</a>
                                @endif
                                @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'staff' && in_array('schools', auth()->user()->permissions ?? [])))
                                <a class="dropdown-item" href="{{ route('schools.index') }}">School Management</a>
                                @endif
                            </div>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                        </li>
                        @endif
                    </ul>
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#" onclick="showInstallInstructions(); return false;">
                                        <i class="fas fa-mobile-alt"></i> Add to Home Screen
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="padding-top: 100px !important;">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
