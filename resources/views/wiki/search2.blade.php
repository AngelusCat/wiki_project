<form action="/search" method="POST">
    @csrf
    <input type="text" name="query" required>
    <input type="submit" value="Найти">
</form>
<br><br>
@if(!empty($newArticleTitles))
@foreach($newArticleTitles as $title => $content)
    <a href="javascript:void(0);" name="{{ $title }}">{{$title}}</a>
    <br>
@endforeach
@endif
<div id="content2" class="test"></div>
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
