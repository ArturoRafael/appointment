<?php

namespace App\Mail;

use App\Http\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class AppointmentCallRecived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $distressCall;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $distressCall)
    {
        $this->distressCall = $distressCall;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        

        return $this->view('mails.appointment_call') 
                     ->attach(public_path().'/invite.ics', [
                    'as' => 'event.ics',
                    'mime' => 'text/calendar;charset=UTF-8;method=REQUEST',
                     ])
                     ->subject($this->distressCall->reason);
    }
}
