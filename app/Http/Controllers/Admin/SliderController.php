<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    /**
     * @var Slider
     */
    private $slider;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * SliderController constructor.
     * @param Slider $slider
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Slider $slider,
        BaseRepository $baseRepository
    )
    {
        $this->slider = $slider;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->slider;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('title', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('content', 'LIKE', '%' . $params['search'] . '%');
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->all();

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = $this->uploadSingleImage($request, 'image', 'slider', 'slider', 1920, 869);

        DB::beginTransaction();
        try {
            $this->slider
                ->create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'link' => $data['link'],
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'status' => $data['status']
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->deleteImage($imageUpload['image_path']);
            Log::error($e->getMessage() . '. Line: ' . $e->getLine());
            return $this->responseJson([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Thêm Slider thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $slider = $this->slider
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $slider = $this->slider
            ->find($data['id']);

        if (!$slider) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Slider không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $slider->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'slider', 'slider', 1920, 869);
        } else {
            $imageUpload['image_path'] = $slider->image_path;
            $imageUpload['image_name'] = $slider->image_name;
        }

        DB::beginTransaction();
        try {
            $slider
                ->update([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'link' => $data['link'],
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'status' => $data['status']
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete old image if not success
            if ($request->file('image')) {
                $this->deleteImage($imageUpload['image_path']);
            }
            Log::error($e->getMessage() . '. Line: ' . $e->getLine());
            return $this->responseJson([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
        }

        // Delete old image if success
        if ($request->file('image')) {
            $this->deleteImage($imagePathOld);
        }

        return $this->responseJson([
            'success' => 1,
            'message' => 'Sửa Slider thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $slider = $this->slider
            ->find($id);

        if (!$slider) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Slider không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $slider->image_path;

        DB::beginTransaction();
        try {
            if ($slider->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Slider không thành công'
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
            'message' => 'Xoá Slider thành công'
        ]);
    }
}
