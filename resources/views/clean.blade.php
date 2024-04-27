<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wiki-парсер</title>
</head>
<body>
{{--    <button id="import">Импорт статей</button>
    <button id="search">Поиск</button>--}}
@include('test3')
    <br>
    <br>
    <div id="content">
{{--        <form method="POST" id="form">
            @csrf
            <input type="text" name="articleName">
            <input type="submit" value="Скопировать">
        </form>
        <script>
            var form = document.getElementById('form');
            var params = new FormData(form);
            fetch('/', {
                method: 'POST',
                body: params
            });
        </script>
        <div>
            @if(isset($articles))
                @foreach($articles as $article)
                    <p>Название: {{$article->title}}</p>
                    <p>Ссылка: {{$article->link}}</p>
                    <p>Размер: {{$article->size}}</p>
                    <p>Количество слов: {{$article->word_count}}</p>
                @endforeach
            @endif
        </div>--}}
        @include('index')
    </div>
    <script>
        const buttonImport = document.getElementById('import');
        const buttonSearch = document.getElementById('search');
        const div = document.getElementById('content');

        buttonImport.addEventListener('click', function () {
            fetch('/index').then(function (response) {
                response.text().then(function (text) {
                    div.innerHTML = text;
                });
            });
        });
        buttonSearch.addEventListener('click', function () {
            fetch('/search').then(function (response) {
                response.text().then(function (text) {
                    div.innerHTML = text;
                });
            });
        });
    </script>
</body>
</html>
