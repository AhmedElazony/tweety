<x-app-layout>

    <x-back-button :back="route('home')"/>

    <header class="relative">
        <div class="relative">
            <img src="{{ asset('images/default-profile-banner.jpg') }}"
                 alt="banner"
                 class="mb-2"
            >

            <img src="{{ $user->avatar }}" alt="user avatar"
                 class="rounded-full mr-2 absolute bottom-0 transform -translate-x-1/2 translate-y-1/2"
                 style="left: 50%"
                 width="150"
            >
        </div>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="font-bold text-2xl">{{ $user->name  }}</h2>
                <p class="text-sm">Joined {{ $user->created_at->diffForHumans() }}</p>
            </div>

            <div class="flex">
                {{-- TODO: user profile url. --}}
                @if(request()->path() === str_replace(' ', '%20', auth()->user()->path()))
                    <a href="{{ route('profile.edit') }}" class="rounded-full border border-gray-200 text-black text-sm mr-2 py-2 px-4 hover:bg-gray-100">Edit Profile</a>
                    <x-logout-form />
                @elseif(auth()->user()->isFollowing($user))
                    <x-unfollow-form :user="$user"/>
                @else
                    <x-follow-form :user="$user"/>
                @endif
            </div>
        </div>

        <p class="text-xs">
            Lorem Ipsum is simply dummy text of the printing and typesetting industry.
            Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
            when an unknown printer took a galley of type and scrambled it to make a type specimen book.
            It has survived not only five centuries, but also the leap into electronic typesetting,
            remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
            sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
            like Aldus PageMaker including versions of Lorem Ipsum.
        </p>
    </header>

    @include('tweets.partials._timeline', [
        'tweets' => $user->tweets
    ])

</x-app-layout>
