<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class GreetingWidget extends Widget
{
    protected string $view = 'filament.widgets.greeting-widget';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $hour = (int) now()->format('H');

        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat pagi';
            $icon = 'heroicon-o-sun';
            $accent = '#f59e0b';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat siang';
            $icon = 'heroicon-o-sun';
            $accent = '#0ea5e9';
        } elseif ($hour >= 15 && $hour < 19) {
            $greeting = 'Selamat sore';
            $icon = 'heroicon-o-cloud';
            $accent = '#f97316';
        } else {
            $greeting = 'Selamat malam';
            $icon = 'heroicon-o-moon';
            $accent = '#6366f1';
        }

        return [
            'greeting' => $greeting,
            'name' => auth()->user()?->name ?? 'Pengguna',
            'icon' => $icon,
            'accent' => $accent,
            'date' => now()->isoFormat('dddd, D MMMM YYYY'),
        ];
    }
}
