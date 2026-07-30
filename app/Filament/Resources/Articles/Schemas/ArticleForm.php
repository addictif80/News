<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Article')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Contenu')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Textarea::make('excerpt')
                                    ->label('Chapô')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                RichEditor::make('content')
                                    ->label('Contenu')
                                    ->columnSpanFull(),
                                FileUpload::make('featured_image')
                                    ->label('Image mise en avant')
                                    ->image()
                                    ->directory('articles'),
                            ]),
                        Tab::make('Classement')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Catégorie')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('author_id')
                                    ->label('Auteur')
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload(),
                                Select::make('tags')
                                    ->label('Tags')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                                Select::make('template_id')
                                    ->label('Template')
                                    ->relationship(
                                        'template',
                                        'name',
                                        modifyQueryUsing: fn ($query) => $query->where('type', 'article'),
                                    )
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Tab::make('Publication')
                            ->schema([
                                Select::make('status')
                                    ->label('Statut')
                                    ->options([
                                        'draft' => 'Brouillon',
                                        'pending_review' => 'En attente de validation',
                                        'published' => 'Publié',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->label('Date de publication'),
                            ]),
                        Tab::make('SEO')
                            ->schema([
                                TextInput::make('seo_title')->label('Titre SEO'),
                                Textarea::make('seo_description')->label('Meta description')->rows(2),
                                FileUpload::make('seo_og_image')->label('Image Open Graph')->image()->directory('seo'),
                                TextInput::make('canonical_url')->label('URL canonique')->url(),
                            ]),
                        Tab::make('Import externe')
                            ->schema([
                                Toggle::make('is_imported')->label('Article importé'),
                                Select::make('source_site_id')
                                    ->label('Site source')
                                    ->relationship('sourceSite', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('source_url')->label("URL d'origine")->url(),
                                Toggle::make('requires_admin_validation')->label('Nécessite une validation admin'),
                            ]),
                    ]),
            ]);
    }
}
