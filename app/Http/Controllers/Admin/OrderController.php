<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResponseTrait;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * OrderController constructor.
     * @param Order $order
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Order $order,
        BaseRepository $baseRepository
    )
    {
        $this->order = $order;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param int $orderStatus
     * @param Request $request
     * @return JsonResponse
     */
    public function list($orderStatus = 0, Request $request)
    {
        $query = $this->order;

        $params = $request->all();

        $total = $query->count();

        // Filter with order status
        if ($orderStatus == 0) {
            $query = $query->where('status', Order::NEW_STATUS);
        } else if ($orderStatus == 1) {
            $query = $query->where('status', Order::TRANSPORT_STATUS);
        } else if ($orderStatus == 2) {
            $query = $query->where('status', Order::DONE_STATUS);
        } else if ($orderStatus == 3) {
            $query = $query->where('status', Order::CANCEL_STATUS);
        }

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('full_name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('email', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('phone_number', 'LIKE', '%' . $params['search'] . '%');
        }

        // Sort
        $query = $this->baseRepository->sort($query, $params);

        $totalResult = $query->count();

        // Paginate
        $result = $this->baseRepository->paginate($query, $params);

        return $this->responseJson([
            'data' => $result['data']->items(),
            'total_result' => $totalResult,
            'total' => $total,
            'page' => $result['page'],
            'last_page' => ceil($totalResult / $result['per_page'])
        ]);
    }

    /**
     * @param $id - order id
     * @param int $orderStatus - order status
     * @return JsonResponse
     */
    public function changeOrderStatus($id = 0, $orderStatus = 0)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Đơn hàng không tồn tại hoặc đã bị xoá'
            ]);
        }

        $order->update([
            'status' => $orderStatus
        ]);

        return $this->responseJson([
            'success' => 1,
            'message' => 'Đổi trạng thái đơn hàng thành công'
        ]);
    }
}
