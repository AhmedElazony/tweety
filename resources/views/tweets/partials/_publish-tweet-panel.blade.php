<div class="border border-blue-400 rounded-lg px-8 py-6 mb-8">
    <form action="/tweets" method="POST">
        @csrf

        <textarea name="body" id="body" class="w-full border-transparent" placeholder="what's up?"></textarea>

        <hr class="my-3">

        @error('body')
            <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
        @enderror

        @if (session('success'))
            <p class="text-blue-500 text-sm mb-2">{{ session('success') }}</p>
        @endif

        <footer class="flex justify-between">
            <a href="{{ route('profile.show', currentUser()->username)  }}">
                <img src="{{ currentUser()->avatar }}" alt="user avatar" class="rounded-full mr-2 w-12 h-11" width="40" height="40">
            </a>

            <button type="submit" class="bg-blue-500 rounded-full shadow text-white py-2 px-4 hover:bg-blue-600">Tweet!</button>
        </footer>

    </form>

</div>
