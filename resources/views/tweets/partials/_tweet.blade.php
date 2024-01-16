<div class="flex p-4 {{ $loop->last ? '' : 'border-b border-b-gray' }}">
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
        <a href="{{ route('tweet.show', $tweet->id) }}">
            <p class="text-xs text-gray-800">{{ '@'.$tweet->user->username  }}</p>
            <h6 class="text-xs mb-4">{{ $tweet->created_at->diffForHumans() }}</h6>

            <div>
                <p class="text-sm">
                    {!! $tweet->body !!}
                </p>
            </div>
        </a>

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

            <a href="{{ route('tweet.show', $tweet->id) }}">
                <x-comment-button />
            </a>
            <span class="ml-1">
                {{ $tweet->comments()->count() ?? 0 }}
            </span>
        </div>
    </div>

</div>
