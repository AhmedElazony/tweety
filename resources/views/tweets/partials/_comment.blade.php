<div class="flex p-4 {{ $loop->last ? '' : 'border-b border-b-gray' }}">
    <div class="mr-2 flex-shrink-0">
        <a href="{{ route('profile.show', $comment->user->username) }}">
            <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.jpg') }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">
        </a>
    </div>

    <div>
        <div class="flex items-center">
            <a class="hover:underline" href="{{ route('profile.show', $comment->user->username) }}">
                <h5 class="font-bold">{{ $comment->user->name }}</h5>
            </a>
            @if($comment->user->slogan ?? false)
                <img src="{{ asset($comment->user->slogan) }}" class="ml-1" alt="" width="23" height="23">
            @endif
        </div>
        <a href="{{ route('tweet.show', $comment->id) }}">
            <p class="text-xs text-gray-800">{{ '@'.$comment->user->username  }}</p>
            <h6 class="text-xs mb-4">{{ $comment->created_at->diffForHumans() }}</h6>

            <div>
                <p class="text-sm">
                    {!! $comment->body !!}
                </p>
            </div>
        </a>

    </div>

</div>
