<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrmTable extends Model
{
    public $timestamps = false;
    public function Columns() {
        return $this->hasMany('App\CrmColumn');
    }
}
