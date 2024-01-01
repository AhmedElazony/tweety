@props(['user'])
<form action="/profiles/{{ $user->name }}/follow" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="user" id="user" value="{{ $user }}">
    <button type="submit" class="bg-blue-700 rounded-full shadow text-white text-sm py-2 px-4 hover:bg-red-500">
        Following
    </button>
</form>
