<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ResponseTrait;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Blog
     */
    private $blog;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Message
     */
    private $message;

    /**
     * DashboardController constructor.
     * @param Product $product
     * @param Blog $blog
     * @param Order $order
     * @param Message $message
     */
    public function __construct(
        Product $product,
        Blog $blog,
        Order $order,
        Message $message
    )
    {
        $this->product = $product;
        $this->blog = $blog;
        $this->order = $order;
        $this->message = $message;
    }

    /**
     * @return JsonResponse
     */
    public function getProductCount()
    {
        $productCount = $this->product
            ->count();

        return $this->responseJson([
            'success' => 1,
            'data' => $productCount
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getBlogCount()
    {
        $blogCount = $this->blog
            ->count();

        return $this->responseJson([
            'success' => 1,
            'data' => $blogCount
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getOrderCount()
    {
        $orderCount = $this->order
            ->count();

        return $this->responseJson([
            'success' => 1,
            'data' => $orderCount
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getMessageCount()
    {
        $messageCount = $this->message
            ->count();

        return $this->responseJson([
            'success' => 1,
            'data' => $messageCount
        ]);
    }

    /**
     * @param $orderStatus
     * @return JsonResponse
     */
    public function getOrderStatusCount($orderStatus)
    {
        $orderStatusCount = $this->order
            ->where('status', $orderStatus)
            ->count();

        return $this->responseJson([
            'success' => 1,
            'data' => $orderStatusCount
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getOrdersLatest()
    {
        $ordersLatest = $this->order
            ->latest()
            ->take(10)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $ordersLatest
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getProductsLatest()
    {
        $productsLatest = $this->product
            ->with(['product_category'])
            ->latest()
            ->take(5)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $productsLatest
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getProductsMostViewed()
    {
        $productsMostViewed = $this->product
            ->with(['product_category'])
            ->orderBy('view_count', 'DESC')
            ->take(5)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $productsMostViewed
        ]);
    }
}
