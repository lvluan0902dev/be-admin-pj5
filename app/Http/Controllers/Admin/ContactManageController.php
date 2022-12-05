<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\NotificationEmail;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactManageController extends Controller
{
    use ResponseTrait;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var NotificationEmail
     */
    private $notificationEmail;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * ContactManageController constructor.
     * @param Message $message
     * @param NotificationEmail $notificationEmail
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Message $message,
        NotificationEmail $notificationEmail,
        BaseRepository $baseRepository
    )
    {
        $this->message = $message;
        $this->notificationEmail = $notificationEmail;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listMessage(Request $request)
    {
        $query = $this->message;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('email', 'LIKE', '%' . $params['search'] . '%');
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
     * @param $id
     * @return JsonResponse
     */
    public function deleteMessage($id)
    {
        $message = $this->message
            ->find($id);

        if (!$message) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Tin nhắn liên hệ không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($message->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Tin nhắn liên hệ không thành công'
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . '. Line: ' . $e->getLine());
            return $this->responseJson([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Xoá Tin nhắn liên hệ thành công'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listNotificationEmail(Request $request)
    {
        $query = $this->notificationEmail;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->Where('email', 'LIKE', '%' . $params['search'] . '%');
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
     * @param $id
     * @return JsonResponse
     */
    public function deleteNotificationEmail($id)
    {
        $notificationEmail = $this->notificationEmail
            ->find($id);

        if (!$notificationEmail) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Email thông báo không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($notificationEmail->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Email thông báo không thành công'
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . '. Line: ' . $e->getLine());
            return $this->responseJson([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Xoá Email thông báo thành công'
        ]);
    }
}
