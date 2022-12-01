<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    /**
     * @var Testimonial
     */
    private $testimonial;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * TestimonialController constructor.
     * @param Testimonial $testimonial
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Testimonial $testimonial,
        BaseRepository $baseRepository
    )
    {
        $this->testimonial = $testimonial;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->testimonial;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('name', 'LIKE', '%' . $params['search'] . '%')
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
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->all();

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = $this->uploadSingleImage($request, 'image', 'testimonial', 'testimonial', 80, 80);

        DB::beginTransaction();
        try {
            $this->testimonial
                ->create([
                    'name' => $data['name'],
                    'content' => $data['content'],
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
            'message' => 'Thêm Khách hàng chứng thực thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get($id)
    {
        $testimonial = $this->testimonial
            ->find($id);

        if (!$testimonial) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Khách hàng chứng thực không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $testimonial
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $testimonial = $this->testimonial
            ->find($data['id']);

        if (!$testimonial) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Khách hàng chứng thực không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $testimonial->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'testimonial', 'testimonial', 80, 80);
        } else {
            $imageUpload['image_path'] = $testimonial->image_path;
            $imageUpload['image_name'] = $testimonial->image_name;
        }

        DB::beginTransaction();
        try {
            $testimonial
                ->update([
                    'name' => $data['name'],
                    'content' => $data['content'],
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
            'message' => 'Sửa Khách hàng chứng thực thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $testimonial = $this->testimonial
            ->find($id);

        if (!$testimonial) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Khách hàng chứng thực không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $testimonial->image_path;

        DB::beginTransaction();
        try {
            if ($testimonial->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Khách hàng chứng thực không thành công'
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
            'message' => 'Xoá Khách hàng chứng thực thành công'
        ]);
    }
}
