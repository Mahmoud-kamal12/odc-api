<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['name','total' , 'level_id' , 'time'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'user_quizzes');
    }

}
