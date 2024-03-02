<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class NotifyAmbassadorListener
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompletedEvent $event): void
    {
        $order = $event->order;

        Mail::send('ambassador', ['order' => $order], function (Message $message) use ($order) {
            $message->subject('New order completed!');
            $message->to($order->ambassador_email);
        });
    }
}