<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'NTicket',
        'title',
        'desc',
        'DateIncident',
        'contactReclamation',
        'NaturNotification',
        'AerportID',
        'status',
        'creatorID',
        'DateCloture',
        'TicketParent'
    ];

    public function aerport()
    {
        return $this->belongsTo(Aerport::class, 'AerportID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'creatorID');
    }

    public function ownerships()
    {
        return $this->hasMany(TicketOwnership::class, 'ticketID');
    }

    public function getStatus()
    {
        switch ($this->status) {
            case 0:
                return 'Open';
            case 1:
                return 'Recovered';
            case 2:
                return 'Cloture';
            case 3:
                return 'Valid';
            default:
                return 'Not Valid';
        }
    }

    public function getStatusChar()
    {
        switch ($this->status) {
            case 0:
                return 'O';
            case 1:
                return 'R';
            case 2:
                return 'C';
            case 3:
                return 'V';
            default:
                return 'V';
        }
    }

    public function getStatusDesign()
    {
        switch ($this->status) {
            case 0:
                return 'open';
            case 1:
                return 'resolved';
            case 2:
                return 'closed';
            case 3:
                return 'validated';
            default:
                return 'nvalidated';
        }
    }

    public function currentOwnerRelation()
    {
        return $this->hasOne(TicketOwnership::class, 'ticketID')->where('statu', 1)->latest();
    }

    public function ticketOwnerShip()
    {
        return $this->hasMany(TicketOwnership::class, 'ticketID');
    }

    // // Get the current user who has the rights to make changes
    // public function getCurrentOwner()
    // {
    //     $ownership = $this->ownerships()->where('statu', 1)->latest()->first();
    //     return $ownership ? $ownership->owner : null;
    // }

    public function getLatestNullStatusOwnership()
    {
        $ownership = $this->ownerships()->whereNull('statu')->latest()->first();
        return $ownership ? true : false;
    }

    public function getLatestNullStatusOwnershipGet()
    {
        return $this->ownerships()->whereNull('statu')->latest()->first();
    }

    public function getOwnerAtDateTime($dateTime)
    {
        // Ensure $dateTime is a Carbon instance
        $dateTime = Carbon::parse($dateTime);

        // Find the latest ownership record where the 'respond_at' date is before or equal to $dateTime
        $ownership = $this->ticketOwnerShip()
            ->where('statu', 1) // Only consider active ownership records
            ->where('respond_at', '<=', $dateTime) // Ownership started before or at the given datetime
            ->orderBy('respond_at', 'desc') // Order by latest 'respond_at'
            ->first();

        // Return the user who owns the ticket at the given datetime, or null if not found
        return $ownership ? $ownership->reserver : null;
    }


    //

    public function analyseLogs()
    {
        return $this->hasMany(AnalyseLog::class, 'TicketID');
    }

    public function latestAnalyseLog()
    {
        return $this->hasOne(AnalyseLog::class, 'TicketID')->latest();
    }

    public function hasAnalyseLogs()
    {
        return $this->analyseLogs()->exists();
    }

    //

    public function recoveryLogs()
    {
        return $this->hasMany(RecoveryLog::class, 'TicketID');
    }

    public function latestRecoveryLog()
    {
        return $this->hasOne(RecoveryLog::class, 'TicketID')->latest();
    }

    public function hasRecoveryLogs()
    {
        return $this->recoveryLogs()->exists();
    }

    //
    public function validation()
    {
        return $this->hasOne(Validation::class, 'TicketID');
    }

    public function hasValidation()
    {
        return $this->validation()->exists();
    }

    //

    public function parent()
    {
        return $this->belongsTo(Ticket::class, 'TicketParent');
    }

    // Recursive method to get all parent tickets

    public function ticketIsValid(){
        // Ensure that we are working with the current ticket instance
        $ticket = $this; // Use the current instance of Ticket

        return (
            $ticket->status >= 2 &&
            $ticket->hasRecoveryLogs() &&
            $ticket->hasAnalyseLogs() &&
            ($ticket->DateIncident ? true : false) && // Valid DateIncident
            (($ticket->hasRecoveryLogs() && $ticket->latestRecoveryLog->dateRecovery) ? true : false) && // Valid latestRecoveryLog date
            ($ticket->DateCloture ? true : false) && // Valid DateCloture
            $ticket->aerport // Aerport exists
        );
    }

}
