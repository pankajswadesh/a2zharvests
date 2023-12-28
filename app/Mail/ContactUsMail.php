<?php

namespace App\Mail;

use App\Model\ContactUsMessagesModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $contact_id;
    public function __construct($contact_id)
    {
        $this->contact_id=$contact_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $info = ContactUsMessagesModel::where("id",$this->contact_id)->first();
        return $this->from('info@a2zharvests.com')
            ->subject('Contact Mail')
            ->view('frontend.mail.contact_us_mail',compact('info'));

    }
}
