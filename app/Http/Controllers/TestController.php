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
    public function clean(): View
    {
        $articles = Article::query()->get(['title', 'link', 'size', 'word_count']);
        return view('clean', compact('articles'));
    }
    public function index(): View
    {
        $articles = Article::query()->get(['title', 'link', 'size', 'word_count']);
        return view('index', compact('articles'));
    }
    public function store(Request $request): ?View
    {
        if ($request->method() === 'GET') {
            $articles = Article::query()->get(['title', 'link', 'size', 'word_count']);
            return view('clean', compact('articles'));
        }

        $query = trim($request->all()['articleName']);

        if (empty($query)) {
            echo 'Ничего не передано';
            die;
        }

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

        //Получить максимальный id в таблице words_atoms до вставки новых записей
        $oldMaxId = WordAtom::query()->count();

        foreach ($wordsAtoms as $word) {
            $line = "\t" . $word . "\n";
            file_put_contents('C:\ProgramData\MySQL\MySQL Server 8.1\Uploads\wordsAtoms.txt', $line, FILE_APPEND);
        }

        try {
            DB::beginTransaction();
            $article = new Article();
            $article->title = $query;
            $article->link = $link;
            $article->size = $size;
            $article->word_count = $numberOfWordsInArticle;
            $article->content = $articleContent;
            $article->save();

            DB::statement("LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.1/Uploads/wordsAtoms.txt' IGNORE INTO TABLE words_atoms (id, word)");

            //Получить максимальный id в таблице words_atoms после вставки новых записей
            $newMaxId = WordAtom::query()->count();

            for ($i = $oldMaxId+1, $a = 0; $i <= $newMaxId, $a < count($numberOfOccurrencesOfWord); $i++, $a++) {
                $line = $article->id . "\t" . $i . "\t" . $numberOfOccurrencesOfWord[$a] . "\n";
                file_put_contents('C:\ProgramData\MySQL\MySQL Server 8.1\Uploads\communications.txt', $line, FILE_APPEND);
            }
            DB::statement("LOAD DATA INFILE 'C:/ProgramData/MySQL/MySQL Server 8.1/Uploads/communications.txt' IGNORE INTO TABLE communications (article_id, word_id, number_of_occurrences)");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
        }

        unlink('C:\ProgramData\MySQL\MySQL Server 8.1\Uploads\wordsAtoms.txt');
        unlink('C:\ProgramData\MySQL\MySQL Server 8.1\Uploads\communications.txt');

        DB::statement("ALTER TABLE words_atoms AUTO_INCREMENT = $newMaxId");

        $articles = Article::query()->get(['title', 'link', 'size', 'word_count']);

        return view('clean', compact('articles'));

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
        dump($articleTitles);
    }

    public function test(): View
    {
        return view('test');
    }

    public function test2(): View
    {
        return view('test2');
    }

    //https://learn.javascript.ru/fetch
    //https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
    //https://habr.com/ru/articles/14246/
    //http://javascript.ru/ajax/intro
    //https://codepen.io/turngait/post/ajax-js
    //https://developer.mozilla.org/ru/docs/Learn/JavaScript/Client-side_web_APIs/Fetching_data
    //https://learn.javascript.ru/ui
}
