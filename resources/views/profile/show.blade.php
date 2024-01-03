<x-app-layout>

    <x-back-button :back="route('home')"/>

    <header class="relative">
        <div class="relative">
            <img src="{{ asset('images/palestine.jpeg') }}"
                 alt="banner"
                 class="mb-2 rounded-2xl"
            >

            <img src="{{ $user->avatar }}" alt="user avatar"
                 class="rounded-full w-50 h-36 mr-2 absolute bottom-0 transform -translate-x-1/2 translate-y-1/2"
                 style="left: 50%"
                 width="150"
            >
        </div>
        <div class="flex justify-between items-center mb-6">
            <div>
                <div class="flex items-center">
                    <h2 class="font-bold text-2xl">{{ $user->name  }}</h2>

                    @if($user->slogan ?? false)
                        <img src="{{ asset($user->slogan) }}" class="ml-1 mt-1 w-7" alt="">
                    @endif

                </div>
                <p class="text-sm text-gray-700">{{ '@'.$user->username }}</p>
            </div>

            <div class="flex">
                @if(currentUser()->is($user))
                    <a href="{{ route('profile.edit') }}" class="rounded-full border border-gray-200 text-black text-sm mr-2 py-2 px-4 hover:bg-gray-100">Edit Profile</a>
                    <x-logout-form />
                @elseif(currentUser()->isFollowing($user))
                    <x-unfollow-form :user="$user"/>
                @else
                    <x-follow-form :user="$user"/>
                @endif
            </div>
        </div>

        <p class="text-sm">
            {{ $user->bio ?? 'الحمد لله ناصر المجاهدين، ومُذل المُستكبرين، والصلاة والسلام على نبينا المجاهد الشهيد محمد ﷺ' }}
        </p>
        <div class="flex text-sm mt-3 mb-3">
            <p class="">{{ $user->followers->count() }} Followers</p>
            <p class="ml-2">{{ $user->following->count() }} Following</p>
        </div>
        <div class="flex shrink-0 relative">
            <svg class="h-4 w-4 text-gray-800 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" data-testid="svg-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path><path d="M16 3l0 4"></path><path d="M8 3l0 4"></path><path d="M4 11l16 0"></path><path d="M11 15l1 0"></path><path d="M12 15l0 3"></path></svg>
            <p class="text-sm text-gray-800 ml-1">Joined {{ $user->created_at->diffForHumans() }}</p>
        </div>
    </header>

    @include('tweets.partials._timeline', [
        'tweets' => $user->tweets
    ])

</x-app-layout>
