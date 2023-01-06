<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = ["name" ,"description","price" ,"image"];

    public function ordersItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function ($value){
                if (Str::contains($value,'placeholder.com')){
                    return $value;
                }else{
                    return asset($value);
                }
            },
        );
    }
}
