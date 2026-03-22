<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldInspectionImage extends Model
{
    protected $fillable = [
        'field_inspection_id',
        'image_path',
        'caption',
    ];

    public function inspection()
    {
        return $this->belongsTo(FieldInspection::class, 'field_inspection_id');
    }
}
