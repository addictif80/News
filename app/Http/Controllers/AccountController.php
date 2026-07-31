<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('site.account.edit', [
            'user' => $user,
            'categories' => Category::orderBy('name')->get(),
            'favoriteCategoryIds' => $user->favoriteCategories()->pluck('categories.id')->all(),
            'vapidPublicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('status', 'Profil mis à jour.');
    }

    public function updateInterests(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_ids' => ['array'],
            'category_ids.*' => ['exists:categories,id'],
        ]);

        $request->user()->favoriteCategories()->sync($data['category_ids'] ?? []);

        return back()->with('status', "Centres d'intérêt mis à jour.");
    }

    public function export(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'profile' => $user->only(['id', 'name', 'email', 'created_at']),
            'favorite_categories' => $user->favoriteCategories()->pluck('name'),
            'comments' => Comment::where('user_id', $user->id)->get(['body', 'status', 'created_at']),
            'newsletter_subscription' => NewsletterSubscriber::where('user_id', $user->id)
                ->get(['email', 'is_confirmed', 'subscribed_at']),
            'subscription' => $user->subscription('default')?->only(['stripe_status', 'created_at']),
        ];

        $filename = 'mes-donnees-'.now()->format('Y-m-d').'.json';

        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($subscription = $user->subscription('default')) {
            if (! $subscription->canceled()) {
                $subscription->cancel();
            }
        }

        $user->pushSubscriptions()->delete();
        $user->favoriteCategories()->detach();
        $user->syncRoles([]);

        $user->update([
            'name' => 'Utilisateur supprimé',
            'email' => 'supprime-'.Str::random(16).'@invalid.local',
            'avatar' => null,
            'password' => Hash::make(Str::random(40)),
            'deleted_data_at' => now(),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Votre compte a été supprimé.');
    }
}
