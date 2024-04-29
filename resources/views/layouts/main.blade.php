<!doctype html>
<html>
<head>
    <title>Wiki-parser</title>
    @vite('resources/css/wiki.css')
    @vite('resources/js/main.js')
</head>
<body>
    <button id="import">Импорт статей</button>
    <button id="search">Поиск</button>
    <br><br>
    <div id="content">
        @yield('content')
    </div>
</body>
</html>
