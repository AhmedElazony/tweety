@props(['trigger'])

<div x-data="{ show: false }" @click.away="show = false" class="relative">
    {{-- Trigger --}}
    <div @click="show = !show">
        {{ $trigger }}
    </div>
    {{-- Links --}}
    <div x-show="show" class="z-[1001] w-36 rounded-md bg-white py-1 shadow-lg transition-opacity duration-100 focus:outline-none dark:bg-gray-900 dark:ring-2 dark:ring-primary-700" style="display: none; position: absolute">
        {{ $slot }}
    </div>
</div>
