
{{--Форма--}}
<form action="/saveArticle" method="POST">
    @csrf
    <input type="text" name="articleName">
    <button type="submit">Скопировать</button>
</form>

{{--Результат обработки--}}
<div>
{{--    @if ($article !== '')
        <p>Импорт завершен</p><br>
        <p>Найдена статья по адресу: {{ $article->link }}</p><br>
        <p>Время обработки: {{ $article->time_of_processing }}</p><br>
        <p>Размер статьи: {{ $article->size }}</p><br>
        <p>Количество слов: {{ $article->word_count }}</p><br>
    @endif--}}
</div>

{{--Таблица--}}
<div id="table">

</div>


<script>
    let table = document.getElementById('table');

    async function updateTableWithArticles() {
        let response = await fetch('/getTable');
        let tableText = await response.text();
        table.innerHTML = tableText;
        await new Promise(resolve => setTimeout(resolve, 50000));
        await updateTableWithArticles();
    }
    updateTableWithArticles();
    //Сделать время между запросами короче.
    //Сделать обработку ошибок
</script>
