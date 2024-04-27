<!doctype html>
<html>
<head>
    <title>Wiki-parser</title>
</head>
<body>
    <button id="import">Импорт статей</button>
    <button id="search">Поиск</button>
    <br><br>
    <div id="content">
        @yield('content')
    </div>
    <script>
        const buttonImport = document.getElementById('import');
        const buttonSearch = document.getElementById('search');
        const div = document.getElementById('content');

        buttonImport.addEventListener('click', function () {
            fetch('/import').then(function (response) {
                response.text().then(function (text) {
                    div.innerHTML = text;
                });
            });
        });
        buttonSearch.addEventListener('click', function () {
            fetch('/showSearch').then(function (response) {
                response.text().then(function (text) {
                    div.innerHTML = text;
                });
            });
        });
    </script>
</body>
</html>
