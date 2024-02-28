<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // It will always return the order items, since it's loaded
        // to get the total admin_revenue
        return OrderResource::collection(Order::with('orderItems')->get());
    }
}