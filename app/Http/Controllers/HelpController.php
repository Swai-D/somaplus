<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        $articles = [];

        foreach ($this->slugs() as $slug) {
            $articles[$slug] = [
                'title' => __("help.articles.{$slug}.title"),
                'summary' => __("help.articles.{$slug}.summary"),
            ];
        }

        return view('help.index', ['articles' => $articles]);
    }

    public function show(string $article): View
    {
        abort_unless(in_array($article, $this->slugs(), true) && Lang::has("help.articles.{$article}.title"), 404);

        return view('help.show', [
            'title' => __("help.articles.{$article}.title"),
            'paragraphs' => trans("help.articles.{$article}.body"),
        ]);
    }

    /**
     * @return list<string>
     */
    private function slugs(): array
    {
        return array_values(array_filter((array) config('help.articles', []), 'is_string'));
    }
}
