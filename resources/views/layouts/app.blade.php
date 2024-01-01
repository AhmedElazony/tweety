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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="">
        {{--            @include('layouts.navigation') --}}

        <!-- Page Heading -->
        <section class="px-8 py-4">
            <header class="container mx-auto">
                <h1>
                    <a href="/">
                        <x-application-logo />
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

</html>
