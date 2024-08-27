<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    use HasFactory;

    protected $fillable = [
        'statu',
        'userID',
        'TicketID',
    ];

    /**
     * Relationship with the User model
     * Assuming that the `userID` is a foreign key referencing the `users` table.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    /**
     * Relationship with the Comment model
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'ValidationID');
    }

    public function getCommentsOrdered()
    {
        return $this->comments()->orderBy('created_at', 'asc')->get();
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'TicketID');
    }
}
