<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;

    private $cartItem;

    public function __construct(
        CartItem $cartItem
    )
    {
        $this->cartItem = $cartItem;
    }

    public function getCart($key = null)
    {
        if ($key) {
            $cart = $this->cartItem
                ->with(['product', 'product_option'])
                ->where('key', $key)
                ->get();

            foreach ($cart as $item) {
                if ($item->product_option_id != null) {
                    $item->totalPrice = $item->quantity * $item->product_option->price;
                } else {
                    $item->totalPrice = $item->quantity * $item->product->option_price;
                }
            }
        } else {
            $cart = '';
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $cart
        ]);
    }
}
