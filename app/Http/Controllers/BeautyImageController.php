<?php

namespace App\Http\Controllers;

use App\Models\BeautyImage;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BeautyImageController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    /**
     * @var BeautyImage
     */
    private $beautyImage;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * BeautyImageController constructor.
     * @param BeautyImage $beautyImage
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        BeautyImage $beautyImage,
        BaseRepository $baseRepository
    )
    {
        $this->beautyImage = $beautyImage;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->beautyImage;

        $params = $request->all();

        $total = $query->count();

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

        $imageUpload = $this->uploadSingleImage($request, 'image', 'beauty-image', 'beauty-image', 500, 500);

        DB::beginTransaction();
        try {
            $this->beautyImage
                ->create([
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
            'message' => 'Thêm Hình ảnh đẹp thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $beautyImage = $this->beautyImage
            ->find($id);

        if (!$beautyImage) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Hình ảnh đẹp không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $beautyImage
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $beautyImage = $this->beautyImage
            ->find($data['id']);

        if (!$beautyImage) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Hình ảnh đẹp không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $beautyImage->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'beauty-image', 'beauty-image', 500, 500);
        } else {
            $imageUpload['image_path'] = $beautyImage->image_path;
            $imageUpload['image_name'] = $beautyImage->image_name;
        }

        DB::beginTransaction();
        try {
            $beautyImage
                ->update([
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'status' => $data['status']
                ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete old image if success
            if ($request->file('image')) {
                $this->deleteImage($imagePathOld);
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
            'message' => 'Sửa Hình ảnh đẹp thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $beautyImage = $this->beautyImage
            ->find($id);

        if (!$beautyImage) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Hình ảnh đẹp không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $beautyImage->image_path;

        DB::beginTransaction();
        try {
            if ($beautyImage->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Hình ảnh đẹp không thành công'
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
            'message' => 'Xoá Hình ảnh đẹp thành công'
        ]);
    }
}
