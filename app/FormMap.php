<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormMap extends Model
{
    public $timestamps = false;
    public function FormRequest() {
        return $this->hasOne('App\FormRequest', 'id', 'form_request_id');
    }
    public function CrmTable() {
        return $this->hasOne('App\CrmTable', 'id', 'crm_table_id');
    }
    public function FormRequestRoute() {
        return $this->hasMany('App\FormRequestRoute');
    }
    public function WebsiteMap() {
        return $this->hasMany('App\WebsiteMap');
    }
}
