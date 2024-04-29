@vite('resources/js/search.js')

<form action="/search" method="POST">
    @csrf
    <input type="text" name="query" required>
    <input type="submit" value="Найти">
</form>

<br><br>

@if(!empty($contentsOfArticles))
    @foreach($contentsOfArticles as $title => $content)
        <a href="#" name="{{ $title }}">{{$title}}</a><br>
    @endforeach
@endif

<div id="articleContent" class="articleContent"></div>
