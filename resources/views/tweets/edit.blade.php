<x-app-layout>
    <x-back-button :back="back()->getTargetUrl()" />

    <h1 class="font-bold text-xl mb-2 underline ml-3">Edit Tweet!</h1>

    @include('tweets.partials._publish-tweet-panel')
</x-app-layout>
