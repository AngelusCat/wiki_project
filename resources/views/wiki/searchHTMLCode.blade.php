@vite('resources/js/search.js')
<form action="/search" method="POST">
    @csrf
    <input type="text" name="query" required>
    <input type="submit" value="Найти">
</form>
<br><br>
@if(!empty($contentsOfArticles))
@foreach($contentsOfArticles as $title => $content)
    <a href="#" name="{{ $title }}">{{$title}}</a>
    <br>
@endforeach
@endif
<div id="articleContent" class="articleContent"></div>
{{--<script>
    const elem = document.querySelectorAll('a');
    const div2 = document.getElementById('content2');
    for ( let i = 0; i < elem.length; i++ ) {
        elem[i].addEventListener( 'click', function () {
            let title = elem[i].name;
            let url = '/getArticleContent/' + title;
            fetch(url).then(function (response) {
                response.text().then(function (text) {
                    div2.innerHTML = text;
                });
            });
        });
    }
</script>--}}
