<x-app-layout>
<x-back-button :back="route('home')" />
<div class="border border-gray-300 rounded-2xl">
    <div class="flex p-4">
        <div class="mr-2 flex-shrink-0">
            <a href="{{ route('profile.show', $tweet->user->username) }}">
                <img src="{{ $tweet->user->avatar ?? asset('images/default-avatar.jpg') }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">
            </a>
        </div>

        <div>
            <div class="flex items-center">
                <a class="hover:underline" href="{{ route('profile.show', $tweet->user->username) }}">
                    <h5 class="font-bold">{{ $tweet->user->name }}</h5>
                </a>
                @if($tweet->user->slogan ?? false)
                    <img src="{{ asset($tweet->user->slogan) }}" class="ml-1" alt="" width="23" height="23">
                @endif
            </div>
            <p class="text-xs text-gray-800">{{ '@'.$tweet->user->username  }}</p>
            <h6 class="text-xs mb-4">{{ $tweet->created_at->diffForHumans() }}</h6>

            <div>
                <p class="text-sm font-semibold">
                    {!! $tweet->body !!}
                </p>
            </div>


            {{-- like --}}
            <div class="flex mt-1 items-center">
                <form action="/tweets/{{$tweet->id}}/like" method="POST" class="flex items-center mr-4">
                    @csrf

                    <button type="submit">
                        @if (currentUser()->liked($tweet))
                            <x-liked />
                        @else
                            <x-non-liked />
                        @endif
                    </button>

                    <span class="ml-1">
                        {{ $tweet->likes ?? 0 }}
                    </span>

                </form>

                {{-- dislike --}}
                <form action="/tweets/{{$tweet->id}}/dislike" method="POST" class="flex items-center">
                    @csrf

                    <button type="submit">
                        <x-dislike :tweet="$tweet" />
                    </button>
                    <span>
                        {{ $tweet->dislikes ?? 0 }}
                    </span>
                </form>

                <x-comment-button />
                <span class="ml-1">
                    {{ $tweet->comments()->count() ?? 0 }}
                 </span>

                <form action="/tweets/{{$tweet->id}}/share" method="POST" class="items-center flex bottom-3 right-3 py-2 px-4">
                    @csrf

                    <button type="submit">
                        <x-share-button :tweet="$tweet" />
                    </button>
                    <p class="ml-1">{{$tweet->shares()->count() ?? 0}}</p>
                </form>

                <div class="flex bottom-3 right-3 py-2 px-4">
                    @if($tweet->user->is(currentUser()))
                        <x-tweet-dropdown :tweet="$tweet" />
                    @endif
                </div>
            </div>
        </div>
        <hr>
    </div>

    <form action="/comments" method="POST" class="flex items-center mb-2">
        @csrf

        <input type="hidden" name="tweet_id" id="tweet_id" value="{{$tweet->id}}">
        <input type="hidden" name="user_id" id="tweet_id" value="{{currentUser()->id}}">

        <textarea name="body" id="body" class="w-3/4 ml-4 rounded-2xl items-center" rows="1" required placeholder="write a comment"></textarea>

        <x-primary-button class="ml-2">Comment</x-primary-button>
    </form>
</div>
    <div class="justify-self-center mt-2">
        <div class="border border-b-gray rounded-2xl">
            @forelse ($tweet->comments as $comment)
                @include('tweets.partials._comment')
            @empty
                <h1 class="px-6 py-6">No Comments!</h1>
            @endforelse
        </div>
    </div>
</x-app-layout>
