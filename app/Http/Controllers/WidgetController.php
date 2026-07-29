<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Widget;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    public function script(string $token): Response
    {
        $widget = Widget::where('token', $token)->firstOrFail();

        $query = Article::query()->published()->latest('published_at');

        if ($widget->category_id) {
            $query->where('category_id', $widget->category_id);
        }

        $articles = $query->limit($widget->articles_count)->get(['id', 'title', 'slug', 'featured_image']);

        $html = view('site.widgets.embed', [
            'widget' => $widget,
            'articles' => $articles,
        ])->render();

        $js = 'document.currentScript.insertAdjacentHTML("afterend", '.json_encode($html).');';

        return response($js, 200)->header('Content-Type', 'application/javascript');
    }
}
