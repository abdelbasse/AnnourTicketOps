<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecoveryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'TicketID',
        'UserID',
        'naruteSolutionID',
        'dateRecovery',
        'repportBody'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'TicketID');
    }

    public function user(){
        return $this->belongsTo(User::class,'UserID');
    }

    public function getNatureSolution()
    {
        return $this->belongsTo(NatureSolution::class, 'naruteSolutionID');
    }

    public function comments()
    {
        return $this->hasMany(RepportComment::class, 'RecoveryIdLog', 'id');
    }
}
