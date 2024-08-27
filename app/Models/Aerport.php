<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aerport extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'location', 'address'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'AerportID');
    }
}
