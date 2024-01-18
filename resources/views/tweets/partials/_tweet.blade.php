@if($sharingUser = \App\Models\User::find($tweet->sharing_user) ?? false)
    <div class=" text-sm font-semibold ml-2">
        <p><a class="hover:underline text-gray-700" href="{{$sharingUser->path()}}">{{'@'.$sharingUser->username}}</a> shared this tweet.</p>
    </div>
@endif

<div class="flex p-4 {{ $loop->last ? '' : 'border-b border-b-gray' }}">
    <div class="mr-2 flex-shrink-0">
        <a href="{{ route('profile.show', $tweet->user->username) }}">
            <img src="{{ $tweet->user->avatar ?? asset('images/default-avatar.jpg') }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">
        </a>
    </div>

    <div>
        <div class="flex items-center">
            <div>
                <a class="hover:underline" href="{{ route('profile.show', $tweet->user->username) }}">
                    <h5 class="font-bold">{{ $tweet->user->name }}</h5>
                </a>
                @if($tweet->user->slogan ?? false)
                    <img src="{{ asset($tweet->user->slogan) }}" class="ml-1" alt="" width="23" height="23">
                @endif
            </div>
        </div>
        <a href="{{ route('tweet.show', $tweet->id) }}">
            <p class="text-xs text-gray-800">{{ '@'.$tweet->user->username  }}</p>
            <h6 class="text-xs mb-4">{{ $tweet->created_at->diffForHumans() }}</h6>

            <div>
                <p class="text-sm font-semibold">
                    {!! $tweet->body !!}
                </p>
            </div>
        </a>


        <div class="flex mt-1 items-center">
            {{-- like --}}
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

            <form action="/tweets/{{$tweet->id}}/share" method="POST" class="items-center flex bottom-3 right-3 py-2 px-4">
                @csrf

                <button type="submit">
                    <x-share :tweet="$tweet" />
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

</div>
