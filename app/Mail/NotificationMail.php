<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;

    public function __construct($notificationId)
    {
        // Fetch notification data using Query Builder
        $this->notification = DB::table('notifications')->where('id', $notificationId)->first();
    }

    public function build()
    {
        return $this->subject($this->notification->title)
                    ->view('emails.notification');
    }
}
