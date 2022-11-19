<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\Message;
use App\Models\NotificationEmail;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactUsController extends Controller
{
    use ResponseTrait;

    /**
     * @var ContactSetting
     */
    private $contactSetting;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var NotificationEmail
     */
    private $notificationEmail;

    /**
     * ContactUsController constructor.
     * @param ContactSetting $contactSetting
     * @param NotificationEmail $notificationEmail
     * @param Message $message
     */
    public function __construct(
        ContactSetting $contactSetting,
        NotificationEmail $notificationEmail,
        Message $message
    )
    {
        $this->contactSetting = $contactSetting;
        $this->notificationEmail = $notificationEmail;
        $this->message = $message;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllContactSetting()
    {
        $contactSettings = $this->contactSetting
            ->where('status', 1)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $contactSettings
        ]);
    }

    public function sendMessage(Request $request)
    {
        $data = $request->all();

        DB::beginTransaction();
        try {
            $this->message
                ->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'content' => $data['content']
                ]);
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
            'message' => 'Gửi tin nhắn thành công'
        ]);
    }

    public function registerNotificationEmail(Request $request)
    {
        $data = $request->all();

        DB::beginTransaction();
        try {
            $this->notificationEmail
                ->create([
                    'email' => $data['email']
                ]);
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
            'message' => 'Đăng ký thông báo thành công'
        ]);
    }
}
