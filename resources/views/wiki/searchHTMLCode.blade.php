@vite('resources/js/search.js')

<form action="/search" method="POST">
    @csrf
    <input type="text" name="query" required>
    <input type="submit" value="Найти">
</form>

<br><br>

@isset($articleTitles)
    @foreach($articleTitles as $articleTitle)
        <a href="#" name="{{ $articleTitle }}">{{ $articleTitle }}</a><br>
    @endforeach
@endisset

<div class="articleContent">
    <pre id="articleContent"></pre>
</div>
