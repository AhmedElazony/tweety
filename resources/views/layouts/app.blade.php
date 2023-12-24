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
        @if (isset($header))
            <section class="px-8 py-4">
                <header class="container mx-auto">
                    {{ $header }}
                </header>
            </section>
        @endif

        <!-- Page Content -->
        <section class="px-8">
            <main>
                {{ $slot }}
            </main>
        </section>
    </div>
</body>

</html>
