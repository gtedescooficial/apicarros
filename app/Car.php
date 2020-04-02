<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model 
{
    const STATUS_DISP = 'DISP';
    const STATUS_NODISP = 'NODISP';
    
    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

}
