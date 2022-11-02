<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    private $slider;

    public function __construct(Slider $slider)
    {
        $this->slider = $slider;
    }

    public function list(Request $request)
    {
        $query = $this->slider->query();

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
            $sort_field = 'created_at';
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

        return $this->responseJson([
            'data' => $result->items(),
            'total_result' => $total_result,
            'total' => $total,
            'page' => $page,
            'last_page' => ceil($total_result / $per_page)
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->all();

        if ($data['status'] == 'true') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        $imageUpload = $this->uploadSingleImage($request, 'image', 'slider', 'slider', 1920, 869);

        DB::beginTransaction();
        try {
            $this->slider->query()
                ->create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'link' => $data['link'],
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'status' => $data['status']
                ]);
            DB::commit();
            return $this->responseJson([
                'success' => 1,
                'message' => 'Thêm Slider thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->deleteImage($imageUpload['image_path']);
            Log::error($e->getMessage() . '. Line: ' . $e->getLine());
            return $this->responseJson([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get($id) {
        $slider = $this->slider
            ->query()
            ->find($id);

        if (!$slider) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Slider không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $slider
        ]);
    }

    public function edit(Request $request) {
        $data = $request->all();
        return $this->responseJson($request);
    }
}
