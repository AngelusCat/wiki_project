<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {
        $response = Http::get("https://ru.wikipedia.org/w/api.php", [
            "action" => "query",
            "prop" => "extracts",
            //"exsentences" => 10,
            "exlimit" => 1,
            "titles" => "Достоевский,_Фёдор_Михайлович",
            "explaintext" => 1,
            "format" => "json"
            //api.php?action=query&prop=extracts&exsentences=10&exlimit=1&titles=Pet_door&explaintext=1&formatversion=2 [try in ApiSandbox]
        ]);
        $test = json_decode($response->body(), JSON_OBJECT_AS_ARRAY)["query"]["pages"][1456]["extract"];

        $replaceArray = [
            "А́" => "А",
            "а́" => "а",
            "Е́" => "Е",
            "е́" => "е",
            "И́" => "И",
            "и́" => "и",
            "О́" => "О",
            "о́" => "о",
            "У́" => "У",
            "у́" => "у",
            "Ы́" => "Ы",
            "ы́" => "ы",
            "Э́" => "Э",
            "э́" => "э",
            "Ю́" => "Ю",
            "ю́" => "ю",
            "Я́" => "Я",
            "я́" => "я"
        ];

        dump(preg_replace('/(А́|а́|Е́|е́|И́|и́|О́|о́|У́|у́|Ы́|ы́|Э́|э́|Ю́|ю́|Я́|я́)/u', '*$0*', $test));

        //Посчитать количество вхождений слова-атома в тексте статьи
        $result = mb_substr_count($test, 'от', 'utf8');

        dump($test);

        /**
         * exlimit
         * explaintext
         * exsectionformat
         */

        /**
         * Тип данных для хранения статей: MEDIUMTEXT
         */

        /**
         * Если в слове присутствует буква с ударением (пример: Миха́йлович), то заменить ее на обычную.
         * Копировать только слова, без пробелов и знаков препинания
         */

        /**
         * [^а-яёА-ЯЁ0-9]+
         * (А́|а́|Е́|е́|И́|и́|О́|о́|У́|у́|Ы́|ы́|Э́|э́|Ю́|ю́|Я́|я́)
         */

        //https://www.mediawiki.org/wiki/API:Get_the_contents_of_a_page
        //https://www.mediawiki.org/wiki/Extension:TextExtracts#API
    }
}
