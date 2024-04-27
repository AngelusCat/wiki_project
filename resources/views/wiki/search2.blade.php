<form action="/search" method="POST">
    @csrf
    <input type="text" name="query">
    <input type="submit" value="Найти">
</form>
