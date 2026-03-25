<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitImage extends Model
{
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
