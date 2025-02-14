<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Helper\ResponseFormatter;

class HomeController extends Controller
{
    public function getSlider()
    {
        $sliders = \App\Models\Slider::all();

        return ResponseFormatter::success($sliders->pluck('api_response'));
    }

    public function getCategory()
    {
        $categories = \App\Models\Category::whereNull('parent_id')->with(['childs'])->get();

        return ResponseFormatter::success($categories->pluck('api_response'));
    }

    public function getProduct()
    {
        $products = \App\Models\Product::orderBy('id', 'desc');

        if (!is_null(request()->category)) {
            $category = \App\Models\Category::where('slug', request()->category)->firstOrFail();
            $products->where('category_id', $category->id);
        }

        if (!is_null(request()->seller)) {
            $seller = \App\Models\User::where('username', request()->seller)->firstOrFail();
            $products->where('seller_id', $seller->id);
        }

        if (!is_null(request()->search)) {
            $products->where('name', 'LIKE', '%' . request()->search . '%');
        }

        $products = $products->paginate(request()->per_page ?? 10);

        return ResponseFormatter::success($products->through(function($product) {
            return $product->api_response_excerpt;
        }));
    }

    public function getProductDetail(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return ResponseFormatter::success($product->api_response);
    }

    public function getProductReview(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $reviews = $product->reviews();

        if (!is_null(request()->rating)) {
            $reviews->where('rating', request()->rating);
        }

        $reviews = $reviews->paginate(request()->per_page ?? 10);

        return ResponseFormatter::success($reviews->through(function($review){
            return $review->api_response;
        }));
    }

    public function getSellerDetail(string $email)
    {
        $seller = \App\Models\Seller::where('email', $email)->firstOrFail();

        return ResponseFormatter::success($seller);
    }



}
