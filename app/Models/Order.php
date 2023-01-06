<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public static  $payment = ['CASH' , 'PAYPAL'];
    public static  $status = ['SUCCESS' , 'CANCELED' , 'WAITING' , 'ERROR' , 'PENDING'];

    protected $fillable = ['user_id','email','city','zip','country','name','total','address','phone','payment_method','status'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

}
