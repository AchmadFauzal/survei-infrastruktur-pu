<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'surveyed_at' => 'datetime',
    ];

    public function photos()
    {
        return $this->hasMany(SurveyPhoto::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyed_by');
    }

    public function assignedSurveyor()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}