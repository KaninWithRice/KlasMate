<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rebyu - Academic Repository</title>
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
<body class="bg-[#f0f0f0] md:flex md:justify-center md:items-start min-h-screen">
    <div class="w-full md:max-w-[390px] min-h-screen bg-white md:shadow-2xl relative overflow-x-hidden mx-auto">
        @if(session('success'))
            <x-alert :message="session('success')" type="success" />
        @endif

        @if(session('error'))
            <x-alert :message="session('error')" type="error" />
        @endif

        @yield('content')
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>
</body>
</html>
