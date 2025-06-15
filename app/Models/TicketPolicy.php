<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPolicy extends Model
{
    protected $fillable = [
        'event_id',
        'price',
        'cancellation_policy', // string or text
        'max_tickets_per_user'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

