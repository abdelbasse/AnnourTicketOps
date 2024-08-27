<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'isLogged',
    ];

    // Define the relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
