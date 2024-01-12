<x-app-layout>
    <p class="font-bold text-xl mb-2 underline">Explore People</p>
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
                    <a href="{{ $user->path()  }}" class="font-bold hover:underline">{{ $user->name }}</a>
                    @if($user->slogan ?? false)
                        <img src="{{ asset($user->slogan) }}" class="ml-1" alt="" width="23" height="23">
                    @endif

                    <p class="text-xs text-gray-800 ml-1">{{ '@'.$user->username }}</p>
                </div>
            </div>

            <div>
                <x-follow-form :user="$user"/>
            </div>
        </div>
    @endforeach
</x-app-layout>
