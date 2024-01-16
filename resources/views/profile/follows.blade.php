<x-app-layout>
    <x-back-button :back="back()->getTargetUrl()" />

    @if(request()->routeIs('followers'))
        <h1 class="font-bold text-xl mb-2 underline">Followers</h1>
    @else
        <h2 class="font-bold text-xl mb-2 underline">Following</h2>
    @endif

    @foreach ($users as $user)
        <div class="flex items-center mb-5 justify-between">
            <div class="flex items-center">
                <a href="{{ $user->path() }}">
                    <img src="{{ $user->avatar }}"
                         alt="{{ $user->username }}'s avatar"
                         width="60"
                         class="mr-4 rounded-full w-10 inline-block"
                         style="max-width: 55px"
                    >
                </a>

                <div>
                    <div class="flex items-center">
                        <a href="{{ $user->path() }}" class="font-bold hover:underline">
                            {{ $user->name }}
                        </a>
                        @if($user->slogan ?? false)
                            <a href="{{ $user->path() }}">
                                <img src="{{ asset($user->slogan) }}" class="ml-1" alt="" width="23" height="23">
                            </a>
                        @endif

                        @if($user->isFollowing(currentUser()) && request()->routeIs('following'))
                            <p class="text-xs bg-gray-300 ml-1 my-0">follows you!</p>
                        @endif
                    </div>


                    <p class="text-xs text-gray-800 ml-1">{{ '@'.$user->username }}</p>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>
