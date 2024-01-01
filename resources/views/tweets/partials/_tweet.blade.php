<div class="flex p-4 {{ $loop->last ? '' : 'border-b border-b-gray' }}">
    <div class="mr-2 flex-shrink-0">
        <a href="{{ route('profile.show', $tweet->user->username) }}">
            <img src="{{ $tweet->user->avatar ?? asset('images/default-avatar.jpg') }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">
        </a>
    </div>

    <div>
        <a href="{{ route('profile.show', $tweet->user->username) }}">
            <h5 class="font-bold">{{ $tweet->user->name }}</h5>
        </a>
        <h6 class="text-xs mb-4">{{ $tweet->created_at->diffForHumans() }}</h6>

        <p class="text-sm">
            {{ $tweet->body }}
        </p>
    </div>

</div>
