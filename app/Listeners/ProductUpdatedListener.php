<?php

namespace App\Listeners;

use App\Events\ProductUpdatedEvent;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductUpdatedListener
{
    public function handle(ProductUpdatedEvent $event): void
    {
        Cache::forget('products_frontend');
        Cache::forget('products_backend');
    }
}