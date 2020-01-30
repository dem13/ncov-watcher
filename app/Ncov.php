<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ncov extends Model
{
    protected $fillable = ['deaths', 'infected', 'cured'];
}
