<div class="border border-gray-300 rounded-2xl mt-5">
    @forelse ($tweets as $tweet)
        @include('tweets.partials._tweet')
    @empty
        <h1 class="px-6 py-6">Your Timeline Is Empty!</h1>
    @endforelse
</div>
<div class="mt-2">
    {{ $tweets->links() }}
</div>
