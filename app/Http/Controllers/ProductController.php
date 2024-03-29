<?php

namespace App\Http\Controllers;

use App\Events\ProductUpdatedEvent;
use App\Models\Product;
use Cache;
use Illuminate\Http\Request;
use Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = Product::create($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent);

        return response($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->update($request->only('title', 'description', 'image', 'price'));

        event(new ProductUpdatedEvent);

        return response($product, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        event(new ProductUpdatedEvent);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Display a listing of the resource to be processed in the frontend.
     */
    public function frontend()
    {
        if ($products = Cache::get('products_frontend')) {
            return $products;
        }

        $products = Product::all();
        Cache::put('products_frontend', $products, 30); // 30s of TTL

        return $products;
    }

    /**
     * Display a paginated listing of the resource processed in the backend.
     * @param Request $request
     * @requestParam string $search
     * @requestParam int $page
     * @requestParam string $sort
     */
    public function backend(Request $request)
    {
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $products = Cache::remember('products_backend', 30, fn () =>  Product::all());
        $sort = $request->input('sort');

        if ($search) {
            $products = $products->filter(fn (Product $product) => Str::contains($product->title, $search) || Str::contains($product->description, $search));
        }

        $total = $products->count();

        switch ($sort) {
            case 'asc':
                $products = $products->sortBy('price');
                break;
            case 'desc':
                $products = $products->sortByDesc('price');
                break;
        }

        return [
            'data' => $products->forPage($page, 15)->values(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'last_page' => ceil($total / 15)
            ]
        ];
    }
}