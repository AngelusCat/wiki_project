<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <form action="/articles" method="POST">
        @csrf
        <input type="text" name="articleName">
        <input type="submit" value="Скопировать">
    </form>
</body>
</html>
