<?php

namespace App\Traits;

trait ResponseTrait
{
    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseJson($data)
    {
        return response()->json($data);
    }
}
