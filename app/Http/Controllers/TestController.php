<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {
/*        $response = Http::get("https://ru.wikipedia.org/w/api.php", [
            "action" => "opensearch",
            "search" => "Достоевский,_Фёдор_Михайлович",
            "limit" => "5",
            "namespace" => "0",
            "format" => "json"
        ]);
        dump($response->collect());*/
    }
}
