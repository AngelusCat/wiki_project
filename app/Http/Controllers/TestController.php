<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function index()
    {

        $titles = "Достоевский,_Фёдор_Михайлович";

        //Пробелы в названии заменять нижнем подчеркиванием

        $response = Http::get("https://ru.wikipedia.org/w/api.php", [
            "action" => "query",
            "prop" => "extracts",
            "exlimit" => 1,
            "titles" => $titles,
            "explaintext" => 1,
            "format" => "json"
            //api.php?action=query&prop=extracts&exsentences=10&exlimit=1&titles=Pet_door&explaintext=1&formatversion=2 [try in ApiSandbox]
        ]);

        // Сделать проверку: найдена статья или нет, если нет, то сообщить об этом пользователю, иначе продолжить
        // обработку

        $articleContent = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $pages = $articleContent['query']['pages'];
        $key = array_key_last($pages);
        $articleContent = $pages[$key]['extract'];

        $replacementArray = [
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
        //Заменяет буквы с ударением на их аналоги без ударения
        $articleContent = strtr($articleContent, $replacementArray);

        //Получить ссылку на статью
        $link = 'https://ru.wikipedia.org/wiki/' . $titles;

        //Разбить текст статьи на слова-атомы
        $wordsAtoms = preg_split('/[^а-яёА-ЯЁ0-9a-zA-Z]+/u', $articleContent, -1, PREG_SPLIT_NO_EMPTY);

        //Посчитать размер
        $size = 0;

        foreach ($wordsAtoms as $word) {
            $size += strlen($word);
        }

        $kbyte = 1024;

        $size = (int) round($size/$kbyte);

        //Посчитать количество вхождений каждого слова-атома
        $numberOfOccurrencesOfWord = array_count_values($wordsAtoms);

        //Посчитать количество слов в статье
        $numberOfWordsInArticle = array_sum($numberOfOccurrencesOfWord);

        $article = new Article();
        $article->title = $titles;
        $article->link = $link;
        $article->size = $size;
        $article->word_count = $numberOfWordsInArticle;
        $article->content = $articleContent;
        $article->save();

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

        //http://www.mysql.ru/docs/man/Using_InnoDB_tables.html

        //ENGINE=InnoDB CHARACTER SET utf8;

        //https://www.mediawiki.org/wiki/API:Get_the_contents_of_a_page
        //https://www.mediawiki.org/wiki/Extension:TextExtracts#API
    }
}
