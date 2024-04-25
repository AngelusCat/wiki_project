<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <form method="POST" id="form">
        @csrf
        <input type="text" name="articleName">
        <input type="submit" value="Скопировать">
    </form>
    <script>
        var form = document.getElementById('form');
        var params = new FormData(form);
        fetch('/', {
            method: 'POST',
            body: params
        });
    </script>
</body>
</html>
