<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['code', 'name'];

    public function subDistricts()
    {
        return $this->hasMany(SubDistrict::class);
    }
}
