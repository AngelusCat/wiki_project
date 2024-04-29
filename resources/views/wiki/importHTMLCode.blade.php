@vite('resources/js/import.js')

<form method="POST" id="form">
    @csrf
    <input type="text" name="articleName" required>
    <input type="submit" value="Скопировать">
</form>

<br><br>

@isset($link)
    <div class="processingResult">
        <p>Импорт завершен.</p><br>
        <p>Найдена статья по адресу: {{ $link }}</p>
        <p>Время обработки: {{ $time }}</p>
        <p>Размер статьи: {{ $size . 'kB'}}</p>
        <p>Количество слов: {{ $numberOfWordsInArticle }}</p>
    </div>
@endisset

<br><br>

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
                        <td @class(['lineСolor' => $loop->even])>{{$article->title}}</td>
                        <td @class(['lineСolor' => $loop->even])>{{$article->link}}</td>
                        <td @class(['lineСolor' => $loop->even])>{{$article->size . 'kB'}}</td>
                        <td @class(['lineСolor' => $loop->even])>{{$article->word_count}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
