<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {
/*        $response = Http::get("https://en.wikipedia.org/w/api.php", [
            "action" => "parse",
            "page" => "100_gecs",
            "format" => "json"
        ]);
        dump($response->body());*/

        //https://www.mediawiki.org/wiki/Manual:Installing_MediaWiki/ru
        //https://www.mediawiki.org/wiki/Extension:TextExtracts#API
        //https://www.mediawiki.org/wiki/API:Get_the_contents_of_a_page
    }
}
