<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['status_survei'];

    protected $casts = [
        'surveyed_at'          => 'datetime',
        'is_coordinates_close' => 'boolean',
        'yolo_detection'       => 'boolean',
        'status_survey'        => 'boolean',
        'status_lapor_big'     => 'boolean',
        'best_score'           => 'float',
    ];

    public function getStatusSurveiAttribute(): string
    {
        return $this->status_survey ? 'sudah' : 'belum';
    }

    public function setStatusSurveiAttribute(string $value): void
    {
        $this->attributes['status_survey'] = $value === 'sudah';
    }

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