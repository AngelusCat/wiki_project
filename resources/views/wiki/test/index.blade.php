<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wiki-parser</title>
</head>
<body>
    <button id="importTab">Импорт статей</button>
    <button id="searchTab">Поиск</button>
    <div id="content">
        @include('wiki.test.import')
    </div>
    <script>
        let importTab = document.getElementById('importTab');
        let searchTab = document.getElementById('searchTab');
        let content = document.getElementById('content');

        importTab.addEventListener('click', function() {
            let htmlCode = fetch('/importHTML').then(function(htmlCode) {
                htmlCode.text().then(function(text) {
                    content.innerHTML = text;
                });
            });
        });
        searchTab.addEventListener('click', function() {
            let htmlCode = fetch('/searchHTML').then(function(htmlCode) {
                htmlCode.text().then(function(text) {
                    content.innerHTML = text;
                });
            });
        });
    </script>
</body>
</html>
