<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormFields extends Model
{
    public $timestamps = false;
    public function Form() {
        return $this->hasOne('App\FormRequest');
    }
}
