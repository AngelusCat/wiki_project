@if(!empty($articles))
    <table border="3">
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
