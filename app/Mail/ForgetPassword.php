<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $name;
    public $otp;
    public function __construct($name,$otp)
    {
        $this->name=$name;
        $this->otp=$otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@a2zharvests.com')
            ->subject('Password Recovery')
            ->view('admin.mail.forget_password_mail')
            ->with([
                'name' => $this->name,
                'otp' => $this->otp,
            ]);

    }
}
