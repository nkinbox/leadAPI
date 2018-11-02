<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormRequest extends Model
{
    public $timestamps = false;
    public function Fields() {
        return $this->hasMany('App\FormFields');
    }
    public function Maps() {
        return $this->hasMany('App\FormMap');
    }
}
