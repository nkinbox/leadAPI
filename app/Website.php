<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    public $timestamps =false;
    protected $table = "tc_websites";
    public function WebsiteMap() {
        return $this->hasMany('App\WebsiteMap');
    }
    public function FormRequest() {
        return $this->belongsToMany('App\FormRequest', 'website_maps');
    }
}
