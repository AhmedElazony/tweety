<div class="border border-gray-300 rounded-lg mt-5">
    @foreach ($tweets as $tweet)
        @include('tweets.partials._tweet')
    @endforeach
</div>
