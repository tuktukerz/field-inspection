<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
                $date = now()->format('Ymd');
                $count = self::whereDate('created_at', now())->count() + 1;
                $model->tower_id = 'MNR-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }
}
