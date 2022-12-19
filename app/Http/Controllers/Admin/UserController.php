<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ResponseTrait;

    /**
     * @var User
     */
    private $user;

    /**
     * UserController constructor.
     * @param User $user
     */
    public function __construct(
        User $user
    )
    {
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeInformation(Request $request)
    {
        $data = $request->all();

        if (!empty($data['current_password'])) {
            if (!Hash::check($data['current_password'], auth()->user()->getAuthPassword())) {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Mật khẩu hiện tại không chính xác'
                ]);
            }

            $this->user
                ->find(auth()->id())
                ->update([
                    'name' => $data['name'],
                    'password' => Hash::make($data['new_password'])
                ]);
        }

        $this->user
            ->find(auth()->id())
            ->update([
                'name' => $data['name'],
            ]);

        return $this->responseJson([
            'success' => 1,
            'message' => 'Đổi thông tin thành công'
        ]);
    }
}
