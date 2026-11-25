<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyLogItem extends Model
{
    protected $fillable = [
        'survey_log_id',
        'infrastructure_id',
        'survey_latitude',
        'survey_longitude',
    ];

    public function log()
    {
        return $this->belongsTo(SurveyLog::class, 'survey_log_id');
    }

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}