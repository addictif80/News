<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Settings\GeneralSettings;
use App\Support\ContentCache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class RssController extends Controller
{
    /**
     * GET /rss.xml — optionally filtered to one or more categories via
     * ?categories=slug-a,slug-b, so the feed can be embedded on an external
     * site scoped to just the topics it wants.
     */
    public function index(Request $request, GeneralSettings $settings): Response
    {
        $slugs = array_filter(explode(',', (string) $request->query('categories')));
        $key = 'rss:'.ContentCache::version().':'.(empty($slugs) ? 'all' : implode(',', $slugs));

        $xml = Cache::remember($key, now()->addMinutes(5), function () use ($slugs, $settings) {
            $query = Article::query()->published()->latest('published_at')->with(['category', 'author']);

            $categories = collect();

            if (! empty($slugs)) {
                $categories = Category::whereIn('slug', $slugs)->get();
                $query->whereHas('category', fn ($q) => $q->whereIn('slug', $slugs));
            }

            $articles = $query->limit(50)->get();

            $title = $settings->site_name.($categories->isNotEmpty() ? ' — '.$categories->pluck('name')->join(', ') : '');

            return view('site.rss.feed', [
                'title' => $title,
                'description' => $settings->site_name,
                'articles' => $articles,
            ])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function category(Request $request, Category $category, GeneralSettings $settings): Response
    {
        $key = 'rss:'.ContentCache::version().':category:'.$category->slug;

        $xml = Cache::remember($key, now()->addMinutes(5), function () use ($category, $settings) {
            $articles = Article::query()
                ->published()
                ->where('category_id', $category->id)
                ->latest('published_at')
                ->with(['category', 'author'])
                ->limit(50)
                ->get();

            return view('site.rss.feed', [
                'title' => $settings->site_name.' — '.$category->name,
                'description' => $category->description ?: $settings->site_name,
                'articles' => $articles,
            ])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
