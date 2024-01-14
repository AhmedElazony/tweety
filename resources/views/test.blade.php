<x-app-layout>
    <div>
        <h1>Hello, World</h1>
        <form action="/test" method="POST">
            @csrf
            <button type="submit">Submit</button>
        </form>
    </div>
    <script>
        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            console.log(JSON.stringify(data));
        });
    </script>
</x-app-layout>
