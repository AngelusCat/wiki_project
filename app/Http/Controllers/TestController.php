<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Communication;
use App\Models\WordAtom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class TestController extends Controller
{
    public function index(): View
    {
        return view('index');
    }
    public function store(Request $request): void
    {
        $query = trim($request->all()['articleName']);
        $titles = str_replace(' ', '_', $query);

        if (Article::query()->where('title', '=', $query)->count() !== 0) {
            echo 'Статья с этим названием уже скопирована.';
            die;
        }

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
        
        if (!array_key_exists('extract', $pages[$key]) || $pages[$key]['extract'] === '') {
            dump('API не нашло статью');
            die;
        }

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
        $wordsAtoms = preg_split('/[^а-яёА-ЯЁ0-9a-zA-Z]+/u', mb_strtolower($articleContent), -1, PREG_SPLIT_NO_EMPTY);

        //Посчитать размер
        $size = 0;

        foreach ($wordsAtoms as $word) {
            $size += strlen($word);
        }

        //$kbyte = 1024;

        //$size = (int) round($size/$kbyte);

        //Посчитать количество вхождений каждого слова-атома
        $numberOfOccurrencesOfWord = array_count_values($wordsAtoms);

        //Убрать повторяющиеся значения в массиве со словами-атомами
        $wordsAtoms = array_unique($wordsAtoms);

        $wordsAtoms = array_values($wordsAtoms);

        //Посчитать количество слов в статье
        $numberOfWordsInArticle = array_sum($numberOfOccurrencesOfWord);

        $numberOfOccurrencesOfWord = array_values($numberOfOccurrencesOfWord);

        try {
            DB::beginTransaction();
            $article = new Article();
            $article->title = $query;
            $article->link = $link;
            $article->size = $size;
            $article->word_count = $numberOfWordsInArticle;
            $article->content = $articleContent;
            $article->save();

            $wordIds = array_map(function (string $word) {
                $wordAtom = new WordAtom();
                $wordAtom->word = $word;
                $wordAtom->save();
                return $wordAtom->id;
            }, $wordsAtoms);

            for ($i = 0; $i < count($wordIds); $i++) {
                $communication = new Communication();
                $communication->article_id = $article->id;
                $communication->word_id = $wordIds[$i];
                $communication->number_of_occurrences = $numberOfOccurrencesOfWord[$i];
                $communication->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
        }

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

    public function showSearch(): View
    {
        return view('search');
    }

    public function searchForm(Request $request): void
    {
        $query = $request->all()['query'];
        $wordIds = WordAtom::query()->where('word', '=', $query)->get('id')->all();
        foreach ($wordIds as $wordId) {
            $test = Communication::query()->where('word_id', '=', $wordId->id)->get(['article_id', 'number_of_occurrences'])->all();
            foreach ($test as $item) {
                $articleIds[$item->article_id] = $item->number_of_occurrences;
            }
        }
        arsort($articleIds);
        foreach ($articleIds as $articleId => $numberOfOccurrences) {
            $test2 = Article::query()->where('id', '=', $articleId)->get('title')->all();
            foreach ($test2 as $item) {
                $articleTitles[] = $item->title;
            }
        }
    }

    //https://learn.javascript.ru/fetch
    //https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
    //https://habr.com/ru/articles/14246/
    //http://javascript.ru/ajax/intro
    //https://codepen.io/turngait/post/ajax-js
    //https://developer.mozilla.org/ru/docs/Learn/JavaScript/Client-side_web_APIs/Fetching_data
    //https://qna.habr.com/q/576205
    //https://qna.habr.com/q/138983

    public function showTest(): View
    {
        return view('test');
    }

    public function test()
    {
        return 'Ok';
    }

}
