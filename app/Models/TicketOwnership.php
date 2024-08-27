<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketOwnership extends Model
{
    protected $fillable = [
        'ticketID',
        'ownerID',
        'reseverID',
        'statu',
        'respond_at',
        'forced'
    ];

    /**
     * Get the ticket associated with the ownership.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticketID');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'ownerID');
    }

    public function reserver()
    {
        return $this->belongsTo(User::class, 'reseverID');
    }
}
