<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Samplecube;

class Scgroup extends Model
{
    //
    public function sample_cubes()
    {
        return $this->hasOne(Samplecube::class,'surveyid','SurveyId');
    }
}
