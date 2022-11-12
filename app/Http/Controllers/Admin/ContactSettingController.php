<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactSettingController extends Controller
{
    use ResponseTrait;

    /**
     * @var ContactSetting
     */
    private $contactSetting;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * ContactSettingController constructor.
     * @param ContactSetting $contactSetting
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        ContactSetting $contactSetting,
        BaseRepository $baseRepository
    )
    {
        $this->contactSetting = $contactSetting;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param $title
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($title)
    {
        $contactSetting = $this->contactSetting
            ->where('title', $title)
            ->first();

        if (!$contactSetting) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Cài đặt liên hệ không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $contactSetting
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $contactSetting = $this->contactSetting
            ->find($data['id']);

        if (!$contactSetting) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Cài đặt liên hệ không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $contactSetting
                ->update([
                    'content' => $data['content'],
                    'status' => $data['status']
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
            'message' => 'Cập nhật Cài đặt liên hệ thành công'
        ]);
    }
}
