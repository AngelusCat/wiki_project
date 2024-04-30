@vite('resources/js/search.js')

<form action="/search" method="POST">
    @csrf
    <input type="text" name="query" required>
    <input type="submit" value="Найти">
</form>

<br><br>

{{--@if(!empty($contentsOfArticles))
    @foreach($contentsOfArticles as $title => $content)
        <a href="#" name="{{ $title }}">{{$title}}</a><br>
    @endforeach
@endif--}}
@isset($articleTitles)
    @foreach($articleTitles as $articleTitle)
        <a href="#" name="{{ $articleTitle }}">{{ $articleTitle }}</a><br>
    @endforeach
@endisset

<div id="articleContent" class="articleContent"></div>
