<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    //
    protected $fillable = [ 
        'user_id', 
        'status', 
        'total', ]; 
    
     /** * Una orden pertenece a un usuario. */ 
     public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
