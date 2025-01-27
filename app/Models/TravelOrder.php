<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'user_id',
        'applicant_name',
        'destination',
        'departure_date',
        'return_date',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
