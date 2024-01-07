<div class="bg-gray-200 rounded-2xl border border-gray-300 py-4 px-6">
    <h3 class="font-bold text-xl mb-4">Follows</h3>

    <ul>
        @forelse (currentUser()->follows() as $user)
            <li class="{{ $loop->last ? '' : 'mb-4'}}">
                <div class="flex items-center">
                    <a href="{{ route('profile.show', $user->username) }}" class="flex items-center text-sm hover:underline">
                        <img src="{{ $user->avatar }}" alt="user avatar" class="rounded-full mr-2 w-11 h-11" width="40" height="40">

                        <h4 class="font-bold">{{ $user->name }}</h4>
                    </a>
                    @if($user->slogan ?? false)
                        <img src="{{ asset($user->slogan) }}" class="ml-1" alt="" width="20" height="20">
                    @endif

                    @if($user->isFollowing(currentUser()))
                        <p class="text-xs bg-gray-300 ml-1">follows you!</p>
                    @endif
                </div>
            </li>
        @empty
            <li>No Friends Yet!</li>
        @endforelse
    </ul>
</div>
