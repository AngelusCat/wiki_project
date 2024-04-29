<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Communication;
use App\Models\WordAtom;
use Illuminate\Http\Request;
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
        $articles = Article::query()->get(['title', 'link', 'size', 'word_count']);
        return view('wiki.importHTMLCode', compact('articles'));
    }
    public function import(Request $request): ?View
    {
        if ($request->method() === 'GET') {
            $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();
            return view('wiki.import', compact('articles'));
        }

        $start = microtime(true);

        $query = trim($request->all()['articleName']);

        $titles = str_replace(' ', '_', $query);

        if (Article::query()->where('title', '=', $query)->count() !== 0) {
/*            $errorWikiParserAlreadyCopied = 'Статья с названием ' . $query . ' уже скопирована.';
            $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();
            return view('wiki.import', compact('articles', 'errorWikiParserAlreadyCopied'));*/
            die;
        }

        $response = Http::get("https://ru.wikipedia.org/w/api.php", [
            "action" => "query",
            "prop" => "extracts",
            "exlimit" => 1,
            "titles" => $titles,
            "explaintext" => 1,
            "format" => "json"
        ]);

        $articleContent = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $pages = $articleContent['query']['pages'];
        $key = array_key_last($pages);

        if (!array_key_exists('extract', $pages[$key]) || $pages[$key]['extract'] === '') {
/*            $errorWikiParserNotFound = 'Система не нашла статью с названием ' . $query;
            $articles = Article::query()->get(['title', 'link', 'size', 'word_count'])->all();
            return view('wiki.import', compact('articles', 'errorWikiParserNotFound'));*/
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

        $kbyte = 1024;

        $size = (int) round($size/$kbyte);

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

        //Преобразовать в переменную для вывода в результате обработки
        $time = round(microtime(true) - $start, 4);

        //мб здесь нужно не подключать Вид, а делать редирект на экшен, который возвращает этот вид, отдавая
        //ему в параметрах url нужные переменные

        return view('wiki.import', compact('articles', 'link', 'size', 'numberOfWordsInArticle', 'time'));
    }

    public function search(Request $request): View
    {
        if ($request->method() === 'GET') {
            $contentsOfArticles = [];
            return view('wiki.search', compact('contentsOfArticles'));
        }

        $query = $request->all()['query'];

        $wordIds = WordAtom::query()->where('word', '=', $query)->get('id')->all();

        if (empty($wordIds)) {
            echo 'Ничего не найдено';
            die;
        }

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

        foreach ($articleTitles as $articleTitle) {
            $result = Article::query()->where('title', '=', $articleTitle)->get(['content'])->all();
            foreach ($result as $item) {
                $contentsOfArticles[$articleTitle] = $item->content;
            }
        }

        return view('wiki.search', compact('contentsOfArticles'));
    }

    public function getArticleContent($title): string
    {
        $articleContent = Article::query()->where('title', '=', $title)->get(['content'])->all()[0];
        return $articleContent->content;
    }
}
