<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <div id="test"></div>
    <script>
        let test = document.getElementById('test');
        window.onload = function()
        {
            let response = fetch('/test').then(function(response) {
                response.text().then(function(text) {
                    test.innerHTML = text;
                });
            });
        }
    </script>
</body>
</html>
