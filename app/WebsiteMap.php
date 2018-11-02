<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteMap extends Model
{
    public $timestamps = false;
    public function FormRequest() {
        return $this->belongsTo('App\FormRequest');
    }
    public function FormMap() {
        return $this->belongsTo('App\FormMap');
    }
}
