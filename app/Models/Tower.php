<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tower extends Model
{
    protected $fillable = [
        'tower_id',
        'location_name',
        'location_detail',
        'kelurahan',
        'kecamatan',
        'latitude',
        'longitude',
        'created_by',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VisitImage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function booted()
    {
        static::creating(function ($model) {
            if (!$model->tower_id) {
                do {
                    $randomId = 'MNR-' . strtoupper(Str::random(6));
                } while (self::where('tower_id', $randomId)->exists());
                
                $model->tower_id = $randomId;
            }

            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::deleting(function ($tower) {
            $tower->visits()->each(fn($visit) => $visit->delete());
        });
    }
}
