<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('template_id')
                    ->label('Template')
                    ->relationship('template', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label('Titre')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                FileUpload::make('featured_image')
                    ->label('Image mise en avant')
                    ->image()
                    ->directory('pages'),
                RichEditor::make('content')
                    ->label('Contenu')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Statut')
                    ->options(['draft' => 'Brouillon', 'published' => 'Publié'])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Date de publication'),
                TextInput::make('seo_title')->label('Titre SEO'),
                Textarea::make('seo_description')->label('Meta description')->rows(2),
                FileUpload::make('seo_og_image')->label('Image Open Graph')->image()->directory('seo'),
                TextInput::make('canonical_url')->label('URL canonique')->url(),
            ]);
    }
}
