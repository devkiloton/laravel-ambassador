<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Link;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Cartalyst\Stripe\Stripe;
use DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // It will always return the order items, since it's loaded
        // to get the total admin_revenue
        return OrderResource::collection(Order::with('orderItems')->get());
    }

    public function store(Request $request)
    {
        if (!$link = Link::where('code', $request->input('code'))->first()) {
            abort(400, 'Invalid code');
        }

        try {
            DB::beginTransaction();

            $order = new Order();

            $order->code = $link->code;
            $order->user_id = $link->user->id;
            $order->ambassador_email = $link->user->email;
            $order->first_name = $request->input('first_name');
            $order->last_name = $request->input('last_name');
            $order->email = $request->input('email');
            $order->address = $request->input('address');
            $order->city = $request->input('city');
            $order->country = $request->input('country');
            $order->zip = $request->input('zip');

            $order->save();

            $lineItems = [];

            foreach ($request->input('products') as $item) {
                $product = Product::find($item['product_id']);

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_title = $product->title;
                $orderItem->price = $product->price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->ambassador_revenue = 0.1 * $product->price * $item['quantity'];
                $orderItem->admin_revenue = 0.9 * $product->price * $item['quantity'];

                $lineItems[] = [
                    'name' => $product->title,
                    'description' => $product->description,
                    'images' => [
                        $product->image
                    ],
                    'amount' => 100 * $product->price,
                    'currency' => 'usd',
                    'quantity' => $item['quantity'],
                ];

                $orderItem->save();
            }

            $stripe = Stripe::make(env('STRIPE_SECRET_KEY'));

            $source = $stripe->checkout()->sessions()->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'success_url' => env('STRIPE_CHECKOUT') . '/success?source={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('STRIPE_CHECKOUT') . '/error',
            ]);

            $order->transaction_id = $source['id'];
            $order->save();

            DB::commit();
            return $source;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function confirm(Request $request)
    {
        $order = Order::where('transaction_id', $request->input('source'))->first();
        if (!$order) {
            return response([
                'error' => 'Order not found'
            ], 404);
        }
        $order->complete = 1;
        $order->save();

        return [
            'message' => 'success'
        ];
    }
}