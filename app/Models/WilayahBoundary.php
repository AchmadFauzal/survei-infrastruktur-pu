<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahBoundary extends Model
{
    protected $fillable = ['kode', 'nama', 'level', 'lat', 'lng', 'geojson'];

    protected $casts = [
        'lat'     => 'float',
        'lng'     => 'float',
        'geojson' => 'array',
    ];
}