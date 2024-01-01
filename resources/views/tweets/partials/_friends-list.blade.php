<div class="bg-gray-200 rounded-lg py-4 px-6">
    <h3 class="font-bold text-xl mb-4">Follows</h3>

    <ul>
        @php
            $followers = auth()->user()->followers->all();
            $following = auth()->user()->following->all();
            $follows = array_merge($followers, $following);
        @endphp
        @forelse ($following as $user)
            <li class="mb-4">
                <div class="flex justify-between">
                    <a href="{{ route('profile.show', $user->name) }}" class="flex items-center text-sm">
                        <img src="{{ $user->avatar }}" alt="user avatar" class="rounded-full mr-2 w-12 h-11" width="40" height="40">

                        {{ $user->name }}
                    </a>
                        {{-- TODO --}}
{{--                    @if(! in_array($user, $following))--}}
{{--                        <div class="flex">--}}
{{--                            <form action="{{ route('follow.store') }}" method="POST" class="">--}}
{{--                                @csrf--}}
{{--                                <input type="hidden" name="user-id" id="user-id" value="{{ $user->id }}">--}}
{{--                                <button type="submit" class="bg-blue-500 rounded-full shadow text-white text-sm py-2 px-4 hover:bg-blue-600">--}}
{{--                                    Follow--}}
{{--                                </button>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                    @endif--}}
                </div>
            </li>
        @empty
            <li>No Friends Yet!</li>
        @endforelse
    </ul>
</div>
