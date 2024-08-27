<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'NTicket',
        'name',
        'mail',
        'tell'
    ];
}
