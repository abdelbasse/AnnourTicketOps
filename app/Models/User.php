<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'Fname',
        'Lname',
        'email',
        'tell',
        'password',
        'password_Org',
        'role',
        'imgUrl'
    ];

    protected $hidden = [
        'password',
        'password_Org',
        'remember_token',
    ];

    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'userId');
    }

    public function latestLoginLog()
    {
        return $this->hasOne(LoginLog::class, 'userId')->latestOfMany();
    }

    public function getRoleAttribute()
    {
        $roleName = 'User';
        if ($this->attributes['role'] <= 2) {
            $roleName = 'Admin';
        } elseif ($this->attributes['role'] == 3) {
            $roleName = 'Supervisor';
        }
        return $roleName;
    }

    public function role()
    {
        return $this->attributes['role'];
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'creatorID');
    }

    public function ownedTickets()
    {
        return $this->hasMany(TicketOwnership::class, 'ownerID');
    }

    public function reservedTickets()
    {
        return $this->hasMany(TicketOwnership::class, 'reseverID');
    }

    // Get all tickets assigned to the user
    public function getAssignedTickets()
    {
        return $this->reservedTickets()->where('statu', 1)->with('ticket')->get()->pluck('ticket');
    }

    public function getOwnedTickets()
    {
        // Use a subquery to get the latest ownership records for each ticket
        $latestOwnerships = TicketOwnership::select('ticketID')
            ->where('reseverID', $this->id)
            ->where('statu', 1)
            ->latest('created_at')
            ->distinct('ticketID')
            ->get();

        // Get the IDs of these tickets
        $ticketIDs = $latestOwnerships->pluck('ticketID');

        // Retrieve the tickets associated with these IDs
        return Ticket::whereIn('id', $ticketIDs)->get();
    }


    // Get all tickets transferred to the user but not yet accepted or declined
    public function getPendingTickets()
    {
        return $this->reservedTickets()->whereNull('statu')->with('ticket')->get()->pluck('ticket');
    }
}
