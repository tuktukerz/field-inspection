<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldInspection extends Model
{
    protected $fillable = [
        'document_number',
        'inspection_date',
        'location_name',
        'location_detail',
        'kelurahan',
        'kecamatan',
        'latitude',
        'longitude',
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

    public function images()
    {
        return $this->hasMany(FieldInspectionImage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function booted()
    {
        static::creating(function ($model) {

            $date = now()->format('Ymd');

            $count = self::whereDate('created_at', now())->count() + 1;

            $model->document_number = 'FI-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        });
    }
}
