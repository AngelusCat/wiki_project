<form method="POST" id="form">
    @csrf
    <input type="text" name="articleName">
    <input type="submit" value="Скопировать">
</form>
<br><br>

<div style="background-color: #cbd5e0">
<p>Импорт завершен.</p>
<br>
<p>Найдена статья по адресу: </p>
<p>Время обработки: </p>
<p>Размер статьи: </p>
<p>Количество слов</p>
</div>
<br><br>

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
        <table border="5">
            <thead>
                <tr>
                    <th>Название статьи</th>
                    <th>Ссылка</th>
                    <th>Размер статьи</th>
                    <th>Кол-во статьи</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td @class(['test2' => $loop->even])>{{$article->title}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->link}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->size}}</td>
                        <td @class(['test2' => $loop->even])>{{$article->word_count}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
