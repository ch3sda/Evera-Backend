<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;
use App\Models\User;
// app/Mail/RequestRejectedMail.php
class RequestRejectedMail extends Mailable
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Your Organizer Request was Rejected')
                    ->view('emails.rejected');
    }
}

