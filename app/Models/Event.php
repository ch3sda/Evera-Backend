<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Define fillable attributes to allow mass assignment
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'event_datetime',
    ];

    // Define the relationship to the EventCategory model
    public function category()
    {
        return $this->belongsTo(EventCategory::class);
    }

    // Define the relationship to the User model (organizer)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // All events in this category
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
