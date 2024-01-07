<x-app-layout><div>
        @foreach ($users as $user)
            <a href="{{ $user->path() }}" class="flex items-center mb-5">
                <img src="{{ $user->avatar }}"
                     alt="{{ $user->username }}'s avatar"
                     width="60"
                     class="mr-4 rounded"
                >

                <div class="flex items-center">
                    <h4 class="font-bold hover:underline">{{ $user->name }}</h4>
                    @if($user->slogan ?? false)
                        <img src="{{ asset($user->slogan) }}" class="ml-1" alt="" width="23" height="23">
                    @endif
                </div>
                <p class="text-xs text-gray-800 ml-1">{{ '@'.$user->username }}</p>
            </a>
        @endforeach

        {{ $users->links() }}
    </div>
</x-app-layout>
