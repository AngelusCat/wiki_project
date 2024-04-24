<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search</title>
</head>
<body>
    <form action="searchForm" method="POST">
        @csrf
        <input type="text" name="query">
        <input type="submit" value="Найти">
    </form>
</body>
</html>
