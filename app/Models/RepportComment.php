<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepportComment extends Model
{
    use HasFactory;
    // The table associated with the model
    protected $table = 'repport_comments';

    // The attributes that are mass assignable
    protected $fillable = [
        'userID',    // Foreign key to the user
        'RecoveryIdLog',
        'comment',   // The content of the comment
    ];

    // If you want to define relationships, you can add them here
    // For example, assuming a comment belongs to a user:
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
