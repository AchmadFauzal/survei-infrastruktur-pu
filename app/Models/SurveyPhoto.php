<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyPhoto extends Model
{
    protected $guarded = ['id'];

    public function infrastructure()
    {
        return $this->belongsTo(Infrastructure::class);
    }
}