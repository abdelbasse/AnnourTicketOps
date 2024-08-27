<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ValidationID',
        'body',
    ];

    /**
     * Relationship with the Validation model
     */
    public function validation()
    {
        return $this->belongsTo(Validation::class, 'ValidationID');
    }
}
