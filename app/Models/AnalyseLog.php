<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyseLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'TicketID',
        'UserID',
        'NSMStatu',
        'naruteIncidentID',
        'equipementID',
        'operatoreID',
        'repportBody'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'TicketID');
    }

    public function getEquipement()
    {
        return $this->belongsTo(Equipement::class, 'equipementID');
    }

    public function getNatureIncident()
    {
        return $this->belongsTo(NatureIncident::class, 'naruteIncidentID');
    }

    public function getOperatore()
    {
        return $this->belongsTo(OperatorTicket::class, 'operatoreID');
    }

    public function user(){
        return $this->belongsTo(User::class,'UserID');
    }

    public function getNSMStatu()
    {
        switch ($this->NSMStatu) {
            case 1:
                return 'Host DOWN';
            case 2:
                return 'Host UP';
            case 3:
                return 'Service Critical';
            case 4:
                return 'Service OK';
            case 5:
                return 'Unknown Status';
            default:
                return 'Status not defined';
        }
    }
}
