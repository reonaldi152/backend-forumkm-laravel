<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getOrCreateCart()
    {
        $cart = Cart::with(['items', 'address'])->where('user_id', auth()->user()->is)->first();
        if (is_null($cart)) {
            $cart = Cart::create([

            ]);
        }
    }
}
