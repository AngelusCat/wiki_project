<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Communication;
use App\Models\WordAtom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class WikiParserController extends Controller
{
    public function getSearchHTMLCode(): View
    {
        return view('wiki.searchHTMLCode');
    }
    public function getImportHTMLCode(): View
    {
        $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();
        return view('wiki.importHTMLCode', compact('articles'));
    }
    public function import(Request $request): View
    {
        if ($request->method() === 'GET') {
            /**
             * @var array<int, Article> $articles массив объектов Модели Article
             */
            $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();

            return view('wiki.import', compact('articles'));
        }

        /**
         * @var  float $start время начала выполнения скрипта
         */
        $start = microtime(true);

        /**
         * @var string $query название статьи
         */
        $query = trim($request->all()['articleName']);

        /**
         * @var string $titles название статьи с подчеркиванием вместо пробела
         */
        $titles = str_replace(' ', '_', $query);

        if (Article::query()->where('title', '=', $query)->count() !== 0) {
            abort(404, 'Статья с названием ' . $query . ' уже скопирована');
        }

        /**
         * @var \Illuminate\Http\Client\Response $response ответ MediaWiki API
         */
        $response = Http::get("https://ru.wikipedia.org/w/api.php", [
            "action" => "query",
            "prop" => "extracts",
            "exlimit" => 1,
            "titles" => $titles,
            "explaintext" => 1,
            "format" => "json"
        ]);

        /**
         * @var array $articleContent информация о запросе в виде массива
         */
        $articleContent = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);

        /**
         * @var array $pages для облегчения работы с многомерным массивом
         */
        $pages = $articleContent['query']['pages'];

        /**
         * @var mixed $key получить доступ к нужному массиву
         */
        $key = array_key_last($pages);

        if (!array_key_exists('extract', $pages[$key]) || $pages[$key]['extract'] === '') {
            abort(404, 'Статья с названием ' . $query . ' не найдена на сайте wikipedia.org');
        }

        /**
         * @var string $articleContent содержимое статьи
         */
        $articleContent = $pages[$key]['extract'];

        /**
         * @var array $replacementArray для замены символов с ударением на аналогичные символы без него
         */
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

        /**
         * @var string $articleContent содержимое статьи без символов с ударением
         */
        $articleContent = strtr($articleContent, $replacementArray);

        /**
         * @var string $link ссылка на статью в wiki
         */
        $link = 'https://ru.wikipedia.org/wiki/' . $titles;

        /**
         * @var array $wordsAtoms слова-атомы
         */
        $wordsAtoms = preg_split('/[^а-яёА-ЯЁ0-9a-zA-Z]+/u', mb_strtolower($articleContent), -1, PREG_SPLIT_NO_EMPTY);

        $size = 0;

        foreach ($wordsAtoms as $word) {
            $size += strlen($word);
        }

        $kbyte = 1024;

        /**
         * @var float $size размер статьи в кб
         */
        $size = round($size/$kbyte, 1);

        /**
         * @var array<string, int> $numberOfOccurrencesOfWord количество вхождений слов-атомов
         */
        $numberOfOccurrencesOfWord = array_count_values($wordsAtoms);

        /**
         * @var array<int, string> $wordsAtoms массив слов-атомов без повторяющихся значений
         */
        $wordsAtoms = array_unique($wordsAtoms);

        /**
         * @var array<int, string> $wordsAtoms массив слов-атомов с последовательными ключами
         */
        $wordsAtoms = array_values($wordsAtoms);

        /**
         * @var int $numberOfWordsInArticle количество слов в статье
         */
        $numberOfWordsInArticle = array_sum($numberOfOccurrencesOfWord);

        /**
         * @var array<int, int> $numberOfOccurrencesOfWord количество вхождений слов-атомов с ключами-числами
         */

        $numberOfOccurrencesOfWord = array_values($numberOfOccurrencesOfWord);

        /**
         * @var int $oldMaxId максимальный id в таблице words_atoms до вставки новых записей в эту таблицу
         */
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

            /**
             * @var int $newMaxId максимальный id в таблице words_atoms после вставки новых записей в эту таблицу
             */
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

        /**
         * @var \Illuminate\Database\Eloquent\Collection $articles коллекция объектов Модели Article
         */

        $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();

        /**
         * @var float $end время конца выполнения скрипта
         */
        $end = round(microtime(true) - $start, 4);

        return view('wiki.import', compact('articles', 'link', 'size', 'numberOfWordsInArticle', 'end'));
    }

    public function search(Request $request): View
    {
        if ($request->method() === 'GET') {
            return view('wiki.search');
        }

        /**
         * @var string $query ключевое слово
         */
        $query = $request->all()['query'];

        /**
         * @var array<int, WordAtom> $wordIds массив объектов Модели WordAtom
         */
        $wordIds = WordAtom::query()->where('word', '=', $query)->get('id')->all();


        if (empty($wordIds)) {
            abort(404, 'WikiParser не содержит статей, в которых есть ключевое слово "' . $query . '".');
        }

        foreach ($wordIds as $wordId) {
            /**
             * @var array<int, Communication> $articleInformation массив объектов Модели Communications
             */
            $articleInformation = Communication::query()->where('word_id', '=', $wordId->id)->get(['article_id', 'number_of_occurrences'])->all();

            foreach ($articleInformation as $article) {
                $articleIds[$article->article_id] = $article->number_of_occurrences;
            }
        }
        arsort($articleIds);
        foreach ($articleIds as $articleId => $numberOfOccurrences) {
            $articleTitle = Article::query()->where('id', '=', $articleId)->get('title')->all();
            foreach ($articleTitle as $article) {
                $articleTitles[] = $article->title;
            }
        }
        return view('wiki.search', compact('articleTitles'));
    }

    public function getArticleContent(string $title): string
    {
        /**
         * @var Article $articleContent объект Модели Article
         */
        $articleContent = Article::query()->where('title', '=', $title)->get(['content'])->all()[0];
        return $articleContent->content;
    }
}
