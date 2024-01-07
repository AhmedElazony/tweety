<form action="{{ route('logout') }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => "rounded-full text-white text-sm bg-red-500 hover:bg-red-700 py-2 px-4"]) }}>Logout</button>
</form>
