<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KlasMate - Academic Repository</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-white min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Navigation (Desktop) -->
        @auth
            @if(!request()->routeIs('login', 'register', 'password.*', 'forgot-password'))
                <div class="hidden md:block w-72 bg-[#fcf0cf] border-r border-black/10 shrink-0">
                    <x-navigation is-sidebar="true" />
                </div>
            @endif
        @endauth

        <!-- Main Content Area -->
        <div class="flex-1 w-full bg-white relative min-h-screen">
            @if(session('success'))
                <x-alert :message="session('success')" type="success" />
            @endif

            @if(session('error'))
                <x-alert :message="session('error')" type="error" />
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    @auth
        @if(!request()->routeIs('login', 'register', 'password.*', 'forgot-password'))
            <x-navigation />
        @endif
    @endauth

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>
</body>
</html>
