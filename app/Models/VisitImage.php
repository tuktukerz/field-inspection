<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class VisitImage extends Model
{
    protected static function booted()
    {
        static::deleted(function ($image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }

    protected $fillable = [
        'visit_id',
        'tower_id',
        'image_path',
        'caption',
        'taken_at',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }
}
