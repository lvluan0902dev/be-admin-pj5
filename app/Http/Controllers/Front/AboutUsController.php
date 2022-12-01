<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    use ResponseTrait;

    /**
     * @var Staff
     */
    private $staff;

    /**
     * AboutUsController constructor.
     * @param Staff $staff
     */
    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    /**
     * @return JsonResponse
     */
    public function getAllStaff()
    {
        $staff = $this->staff
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $staff
        ]);
    }
}
