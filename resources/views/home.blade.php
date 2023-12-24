@props(['tweets'])
<x-app-layout>
    <x-slot name="header">
        <h1>
            <img src="{{ asset('images/logo.svg') }}" alt="Tweety" />
        </h1>
    </x-slot>
    <div class="lg:flex lg:justify-center">
        <div class="lg:w-1/6">
            @include('app._sidebar-links')
        </div>

        <div class="lg:flex-1 lg:mx-10" style="max-width: 700px">
            @include('app._publish-tweet-panel')

            <div class="border border-gray-300 rounded-lg">
                @foreach ($tweets as $tweet)
                    @include('app._tweet')
                @endforeach
            </div>
        </div>

        <div class="lg:w-1/6">
            @include('app._friends-list')
        </div>
    </div>
</x-app-layout>
