<div class="border border-gray-300 rounded-2xl mt-5">
    @forelse ($tweets as $tweet)
        @if($tweet->isShared())
            @foreach($tweet->shares()->orderByDesc($tweet->sharedAt())->pluck('user_id') as $user)
                @include('tweets.partials._tweet', [
                    'sharingUser' => \App\Models\User::find($user)
                ])
            @endforeach
        @endif

        @include('tweets.partials._tweet')
    @empty
        <h1 class="px-6 py-6">Your Timeline Is Empty!</h1>
   @endforelse
</div>
<div class="mt-2 mb-2">
    {{ $tweets->links() }}
</div>
