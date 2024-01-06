<ul class="">
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2 {{ request()->routeIs('home') ? 'bg-gray-200' : ''  }}" href="{{ route('home')  }}">Home</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2" href="/explore">Explore</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2" href="#">Notifications</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2" href="#">Messages</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2" href="#">Bookmarks</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2" href="#">Lists</a></li>
    <li><a class="px-3 font-bold text-lg mb-4 block hover:bg-gray-200 rounded-full w-1/2 {{ request()->url() === route('profile.show', currentUser()->username) ? 'bg-gray-200' : ''  }}" href="{{ route('profile.show', currentUser()->username) }}">Profile</a></li>
    <li><a class="px-3 font-bold text-lg block hover:bg-gray-200 rounded-full w-1/2" href="#">More</a></li>
</ul>
