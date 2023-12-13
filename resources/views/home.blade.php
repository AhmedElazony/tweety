<x-app-layout>
    <x-slot name="header">
        <h1>
            <img
                src="{{ asset('images/logo.svg') }}"
                alt="Tweety"
            />
        </h1>
    </x-slot>
    <div class="lg:flex">
        <div class="lg:w-1/4">
            <x-_sidebar-links />
        </div>

        <div class="lg:flex-1">2</div>

        <div class="lg:w-1/4">
            <x-_friends-list />
        </div>
    </div>
</x-app-layout>
