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
                <h2 class="font-bold text-2xl">{{ $user->name  }}</h2>
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
        <p class="text-sm mt-2">Joined {{ $user->created_at->diffForHumans() }}</p>
    </header>

    @include('tweets.partials._timeline', [
        'tweets' => $user->tweets
    ])

</x-app-layout>
