@vite('resources/js/import.js')
<form method="POST" id="form">
    @csrf
    <input type="text" name="articleName" required>
    <input type="submit" value="Скопировать">
</form>
<br><br>
@if(isset($link))
<div style="background-color: #cbd5e0">
<p>Импорт завершен.</p>
<br>
<p>Найдена статья по адресу: {{ $link }}</p>
<p>Время обработки: {{ $time }}</p>
<p>Размер статьи: {{ $size . 'kB'}}</p>
<p>Количество слов: {{ $numberOfWordsInArticle }}</p>
</div>
@endif
<br><br>
@if(isset($errorWikiParserAlreadyCopied))
    <p>{{ $errorWikiParserAlreadyCopied }}</p>
@endif
@if(isset($errorWikiParserNotFound))
    <p>{{ $errorWikiParserNotFound }}</p>
@endif
{{--<script>
    var form = document.getElementById('form');
    var params = new FormData(form);
    fetch('/', {
        method: 'POST',
        body: params
    });
</script>--}}
<div>
    @if(!empty($articles))
        <table border="5">
            <thead>
                <tr>
                    <th>Название статьи</th>
                    <th>Ссылка</th>
                    <th>Размер статьи</th>
                    <th>Кол-во слов</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td @class(['test2' => $loop->even])>{{$article->title}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->link}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->size . 'kB'}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->word_count}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
