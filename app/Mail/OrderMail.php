<?php

namespace App\Mail;

use App\Model\ContactUsMessagesModel;
use App\Model\OrderModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order_id;
    public function __construct($order_id)
    {
        $this->order_id=$order_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order = OrderModel::where("id",$this->order_id)->first();
        return $this->from('info@a2zharvests.com')
            ->subject('Order Mail')
            ->view('frontend.mail.order-mail',compact('order'));

    }
}
