<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    private $slider;

    public function __construct(Slider $slider)
    {
        $this->slider = $slider;
    }

    public function list(Request $request) {
        $query = Slider::query();

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('title', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('content', 'LIKE', '%' . $params['search'] . '%');
        }

        // Sort
        if (isset($params['sort_field']) && !empty($params['sort_field'])) {
            $sort_field = $params['sort_field'];
        } else {
            $sort_field ='created_at';
        }

        if (isset($params['sort_type']) && !empty($params['sort_type'])) {
            $sort_type = $params['sort_type'];
        } else {
            $sort_type = 'DESC';
        }

        $query = $query->orderBy($sort_field, $sort_type);

        $total_result = $query->count();

        // Paginate
        if (isset($params['per_page']) && !empty($params['per_page'])) {
            $per_page = $params['per_page'];
        } else {
            $per_page = 10;
        }

        if (isset($params['first_row']) && !empty($params['first_row'])) {
            $first_row = $params['first_row'];
        } else {
            $first_row = 0;
        }

        $page = $first_row / $per_page + 1;
        $result = $query->paginate($per_page, ['*'], 'page', $page);

        return response()->json([
            'data' => $result->items(),
            'total_result' => $total_result,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total_result / $per_page)
        ]);
    }
}