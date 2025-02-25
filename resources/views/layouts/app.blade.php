<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme-mode="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- PWA -->
    @laravelPWA

    <!-- Scripts -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.18/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div>
        <!-- Page Heading -->
        <section class="px-8 py-0 mb-4 bg-gray-300 border border-gray-300 rounded-md">
            <header class="container mx-auto flex justify-between px-2 py-2 items-center">
                <h1>
                    <a href="/">
                        <x-application-logo class="inline-block"/>
                    </a>
                </h1>

                <h1>
                    <a class="flex items-center" href="{{ currentUser()->path()  }}">
                        @if(currentUser()->slogan ?? false)
                            <img src="{{ asset(currentUser()->slogan) }}" class="mr-1" alt="" width="23" height="23">
                        @endif
                        <h3 class="font-bold mr-2 hover:underline">{{ currentUser()->name  }}</h3>

                        <img src="{{ currentUser()->avatar ?? asset('images/default-avatar.jpg') }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">
                    </a>
                </h1>
            </header>
        </section>


        <!-- Page Content -->
        <section class="px-8">
            <main>
                <div class="lg:flex lg:justify-center">
                    <div class="lg:w-1/6">
                        @include('tweets.partials._sidebar-links')
                    </div>

                    <div class="lg:flex-1 lg:mx-10" style="max-width: 700px">
                        {{ $slot }}
                    </div>

                    <div class="lg:w-1/6">
                        @include('tweets.partials._friends-list')
                    </div>
                </div>
            </main>
        </section>
    </div>
</body>
{{--<script src="{{ asset('build/assets/app-b1941ff8.js') }}"></script>--}}
<script>
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    return registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: '{{ config('webpush.vapid.public_key') }}'
                    });
                })
                .then(function(subscription) {
                    // Send subscription info to server
                    fetch('/push-subscription', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(subscription)
                    });
                })
                .catch(function(error) {
                    console.error('Service Worker Error:', error);
                });
        });
    }
</script>
</html>
