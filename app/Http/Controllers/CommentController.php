<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Settings\CommentModerationSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function store(Request $request, Article $article, CommentModerationSettings $settings): RedirectResponse
    {
        abort_if($request->user()->is_comment_banned, 403, "Vous n'êtes plus autorisé à publier des commentaires.");

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $isBlocked = collect($settings->blocked_words)
            ->filter()
            ->contains(fn (string $word) => Str::contains($data['body'], $word, ignoreCase: true));

        Comment::create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
            'status' => $isBlocked ? 'rejected' : 'pending',
        ]);

        return back()->with('status', $isBlocked
            ? 'Votre commentaire contient des termes non autorisés et a été rejeté.'
            : 'Votre commentaire a été soumis et sera visible après modération.');
    }
}
