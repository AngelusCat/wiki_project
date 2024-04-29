<form action="/search" method="POST">
    @csrf
    <input type="text" name="query">
    <input type="submit" value="Найти">
</form>
<br><br>
@if(!empty($newArticleTitles))
@foreach($newArticleTitles as $title => $content)
    <a href="javascript:void(0);" name="{{ $title }}">{{$title}}</a>
    <br>
@endforeach
@endif
<div id="content2" class="test">

</div>
{{--
<script>
    var elem = document.querySelectorAll('a');
    var result = document.getElementById('content');

    for ( var i = 0; i < elem.length; i++ ) { // пробегаемся по всем найденным ссылкам
        elem[i].addEventListener( 'click',(function (e){ // действие при клике
            e.preventDefault(); // Отменяем переход по ссылке
            result = this.innerHTML; // вставляем содержимое ссылки в инпут
        }));
    }
</script>--}}
<script>
    //elem.getAttribute(name)
    const elem = document.querySelectorAll('a');
    const div2 = document.getElementById('content2');
    for ( let i = 0; i < elem.length; i++ ) {
        elem[i].addEventListener( 'click', function () {
            let title = elem[i].name;
            let url = '/get/' + title;
            //console.log(url);
            fetch(url).then(function (response) {
                response.text().then(function (text) {
                    div2.innerHTML = text;
                });
            });
        });
    }
</script>
