<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class recovery_image extends Model
{
   /**
     * The attributes that are NOT mass assignable.
     *
     * @var array
   */
   protected $guarded = [
    	'id'
   ];

   protected $table = 'recovery_image';

   public function user()
   {
   	return $this->belongsTo('App\User', 'user_id');
   }
}
