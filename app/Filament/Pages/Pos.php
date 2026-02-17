<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Support\Enums\Width;
use Filament\Schemas\Schema;

class Pos extends Page
{
    protected string $view = 'filament.pages.pos';
    // protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $title = 'POS';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'POS';

    // protected static ?int $navigationSort = 1;

    // protected static ?string $navigationGroup = 'Transaction';
    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 4,
            'xl' => 5,
        ];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('edit')
    //             ->url(route('posts.edit', ['post' => $this->post])),
    //         Action::make('delete')
    //             ->requiresConfirmation()
    //             ->action(fn () => $this->post->delete()),
    //     ];
    // }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            
        ]);
    }
}
