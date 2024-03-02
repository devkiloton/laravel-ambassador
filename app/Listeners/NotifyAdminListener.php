<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class NotifyAdminListener
{

    /**
     * Handle the event.
     */
    public function handle(OrderCompletedEvent $event): void
    {
        $order = $event->order;

        Mail::send('admin', ['order' => $order], function (Message $message) {
            $message->subject('New order completed!');
            $message->to('admin@example.com');
        });
    }
}