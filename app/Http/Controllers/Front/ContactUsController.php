<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    use ResponseTrait;

    /**
     * @var ContactSetting
     */
    private $contactSetting;

    /**
     * ContactUsController constructor.
     * @param ContactSetting $contactSetting
     */
    public function __construct(ContactSetting $contactSetting)
    {
        $this->contactSetting = $contactSetting;
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
}
