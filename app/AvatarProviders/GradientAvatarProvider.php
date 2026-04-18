<?php

namespace App\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class GradientAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        $name = Filament::getNameForDefaultAvatar($record);

        $palette = [
            '0ea5e9',
            '10b981',
            'f59e0b',
            'ef4444',
            '8b5cf6',
            'ec4899',
            '14b8a6',
            'f97316',
            '6366f1',
            '84cc16',
        ];

        $index = abs(crc32($name)) % count($palette);
        $bg = $palette[$index];

        $initials = str($name)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->take(2)
            ->join('');

        return 'https://ui-avatars.com/api/?'
            . http_build_query([
                'name' => $initials,
                'color' => 'ffffff',
                'background' => $bg,
                'bold' => 'true',
                'size' => 128,
            ]);
    }
}
