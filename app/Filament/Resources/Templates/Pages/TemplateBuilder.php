<?php

namespace App\Filament\Resources\Templates\Pages;

use App\Filament\Resources\Templates\TemplateResource;
use App\Models\Template;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class TemplateBuilder extends Page
{
    protected static string $resource = TemplateResource::class;

    protected string $view = 'filament.resources.templates.pages.template-builder';

    public Template $record;

    public function mount(Template $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Design — '.$this->record->name;
    }

    public function save(string $html, string $css, array $grapesjsData): void
    {
        $this->record->update([
            'html' => $html,
            'css' => $css,
            'grapesjs_data' => $grapesjsData,
        ]);

        Notification::make()
            ->title('Template enregistré')
            ->success()
            ->send();
    }

    public function blockDefinitions(): array
    {
        return match ($this->record->type) {
            'article' => [
                ['id' => 'article-title', 'label' => 'Titre', 'content' => '<h1 class="article-title">{{title}}</h1>'],
                ['id' => 'article-image', 'label' => 'Image mise en avant', 'content' => '<img class="article-featured-image" src="{{featured_image}}" alt="{{title}}" />'],
                ['id' => 'article-content', 'label' => 'Contenu', 'content' => '<div class="article-content">{{content}}</div>'],
                ['id' => 'article-date', 'label' => 'Date', 'content' => '<time class="article-date">{{date}}</time>'],
                ['id' => 'article-author', 'label' => 'Auteur', 'content' => '<div class="article-author">{{author}}</div>'],
            ],
            'page' => [
                ['id' => 'page-title', 'label' => 'Titre', 'content' => '<h1 class="page-title">{{title}}</h1>'],
                ['id' => 'page-image', 'label' => 'Image mise en avant', 'content' => '<img class="page-featured-image" src="{{featured_image}}" alt="{{title}}" />'],
                ['id' => 'page-content', 'label' => 'Contenu', 'content' => '<div class="page-content">{{content}}</div>'],
                ['id' => 'page-date', 'label' => 'Date', 'content' => '<time class="page-date">{{date}}</time>'],
            ],
            'homepage' => [
                ['id' => 'article-card-grid', 'label' => 'Grille de cartes article', 'content' => '<div class="article-card-grid" data-block="article-card-grid" data-category="" data-columns="3" data-rows="2"></div>'],
                ['id' => 'alert-banner', 'label' => "Bandeau d'alerte", 'content' => '<div class="alert-banner-slot" data-block="alert-banner"></div>'],
                ['id' => 'newsletter-signup', 'label' => 'Inscription newsletter', 'content' => '<div class="newsletter-signup-slot" data-block="newsletter-signup"></div>'],
                ['id' => 'subscription-cta', 'label' => 'Abonnement', 'content' => '<div class="subscription-cta-slot" data-block="subscription-cta"></div>'],
                ['id' => 'auth-widget', 'label' => 'Connexion / Inscription', 'content' => '<div class="auth-widget-slot" data-block="auth-widget"></div>'],
            ],
            default => [],
        };
    }
}
