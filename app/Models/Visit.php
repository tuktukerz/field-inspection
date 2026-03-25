<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    public static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::deleting(function ($visit) {
            $visit->images()->each(fn($image) => $image->delete());
        });
    }

    protected $fillable = [
        'tower_id',
        'inspection_date',
        'location_type',
        'observation_distance',
        'tower_type',

        'bolt_count',
        'bolt_condition',
        'bolt_position',

        'frame_condition',
        'frame_maintenance',
        'frame_rust',
        'frame_porous',

        'joint_maintenance',
        'joint_rust',
        'joint_porous',

        'panel_structure',
        'panel_status',
        'lamp_frame',

        'notes',
        'construction_feasibility',
        'follow_up_action',
        'created_by',
    ];

    public function tower(): BelongsTo
    {
        return $this->belongsTo(Tower::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VisitImage::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
