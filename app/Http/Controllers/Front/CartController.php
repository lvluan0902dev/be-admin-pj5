<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ResponseTrait;

    /**
     * @var CartItem
     */
    private $cartItem;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var OrderItem
     */
    private $orderItem;

    /**
     * CartController constructor.
     * @param CartItem $cartItem
     * @param Order $order
     * @param OrderItem $orderItem
     */
    public function __construct(
        CartItem $cartItem,
        Order $order,
        OrderItem $orderItem
    )
    {
        $this->cartItem = $cartItem;
        $this->order = $order;
        $this->orderItem = $orderItem;
    }

    /**
     * @param null $key
     * @return JsonResponse
     */
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

    /**
     * @param $key
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart($key, Request $request)
    {
        $data = $request->all();

        $cartItemExist = $this->cartItem
            ->where('key', $key)
            ->where('product_id', $data['product_id'])
            ->where('product_option_id', $data['product_option_id'])
            ->first();

        if ($cartItemExist) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Sản phẩm đã có trong giỏ hàng'
            ]);
        }

        $this->cartItem->create([
            'product_id' => $data['product_id'],
            'product_option_id' => $data['product_option_id'],
            'quantity' => $data['quantity'],
            'key' => $key
        ]);

        return $this->responseJson([
            'success' => 1,
            'data' => $key
        ]);
    }

    /**
     * @param $id - item id
     * @return JsonResponse
     */
    public function removeItem($id)
    {
        $cartItem = $this->cartItem->find($id);

        $cartItem->delete();

        return $this->responseJson([
            'success' => 1,
            'data' => $cartItem->id
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function minusItem($id, Request $request)
    {
        $cartItem = $this->cartItem->find($id);

        if ($cartItem->quantity > 1) {
            $cartItem->update([
                'quantity' => $cartItem->quantity - 1
            ]);
        } else {
            $cartItem->delete();
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Update quantity cart item success'
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function plusItem($id, Request $request)
    {
        $cartItem = $this->cartItem->find($id);

        $cartItem->update([
            'quantity' => $cartItem->quantity + 1
        ]);

        return $this->responseJson([
            'success' => 1,
            'message' => 'Update quantity cart item success'
        ]);
    }

    /**
     * @param null $key
     * @return JsonResponse
     */
    public function getCartTotalPrice($key = null)
    {
        $cartItems = $this->cartItem
            ->with(['product', 'product_option'])
            ->where('key', $key)
            ->get();

        $totalPrice = 0;

        if ($cartItems) {
            foreach ($cartItems as $item) {
                if ($item->product_option_id != null) {
                    $totalPrice += $item->quantity * $item->product_option->price;
                } else {
                    $totalPrice = $item->quantity * $item->product->option_price;
                }
            }
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $totalPrice
        ]);
    }

    public function cartOrder(Request $request)
    {
        $data = $request->all();

        if (empty($data['key'])) {
            return $this->responseJson([
                'success' => 1,
                'message' => 'Key invalid'
            ]);
        }

        $order = $this->order
            ->create([
                'full_name' => $data['full_name'],
                'address' => $data['address'],
                'phone_number' => $data['phone_number'],
                'email' => $data['email'],
                'status' => Order::NEW_STATUS
            ]);

        $cartItems = $this->cartItem
            ->with(['product', 'product_option'])
            ->where('key', $data['key'])
            ->get();

        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if ($item->product_option_id != null) {
                    $this->orderItem
                        ->create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'product_option_id' => $item->product_option_id,
                            'product_name' => $item->product->name,
                            'option_name' => $item->product_option->name,
                            'option_price' => $item->product_option->price,
                            'quantity' => $item->quantity
                        ]);
                } else {
                    $this->orderItem
                        ->create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'product_option_id' => null,
                            'product_name' => $item->product->name,
                            'option_name' => $item->product->option_name,
                            'option_price' => $item->product->option_price,
                            'quantity' => $item->quantity
                        ]);
                }
            }

            $this->cartItem
                ->with(['product', 'product_option'])
                ->where('key', $data['key'])
                ->delete();
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Đặt hàng thành công'
        ]);
    }
}
