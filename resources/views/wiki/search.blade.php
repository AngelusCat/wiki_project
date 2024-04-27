@extends('layouts.main')

@section('content')
    <form action="searchForm" method="POST">
        @csrf
        <input type="text" name="query">
        <input type="submit" value="Найти">
    </form>
@endsection
