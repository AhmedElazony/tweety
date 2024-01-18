@props(['tweet'])

<x-dropdown>
    <x-slot name="trigger">
        <button class="flex items-center">
            <x-dots />
        </button>
    </x-slot>
    <x-dropdown-link href="/tweets/{{$tweet->id}}/edit">edit</x-dropdown-link>
    <form action="/tweets/{{$tweet->id}}/delete" method="POST">
        @csrf
        @method('DELETE')
        <div class="hover:bg-blue-400">
            <button class="text-red-500 ml-2">Delete</button>
        </div>
    </form>
</x-dropdown>
