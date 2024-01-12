@props(['user'])
<form action="/{{ $user->username }}/follow" method="POST">
    @csrf
    <input type="hidden" name="user" id="user" value="{{ $user }}">
    <button type="submit" class="bg-blue-500 rounded-full shadow text-white text-sm py-2 px-4 hover:bg-blue-600 ">
        Follow
    </button>
</form>
