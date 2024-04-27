<form method="POST" id="form">
    @csrf
    <input type="text" name="articleName">
    <input type="submit" value="Скопировать">
</form>
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
{{--        @foreach($articles as $article)
            <p>Название: {{$article->title}}</p>
            <p>Ссылка: {{$article->link}}</p>
            <p>Размер: {{$article->size}}</p>
            <p>Количество слов: {{$article->word_count}}</p>
        @endforeach--}}
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
