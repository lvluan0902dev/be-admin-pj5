<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    /**
     * @var Staff
     */
    private $staff;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * StaffController constructor.
     * @param Staff $staff
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Staff $staff,
        BaseRepository $baseRepository
    )
    {
        $this->staff = $staff;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->staff;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('name', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('position', 'LIKE', '%' . $params['search'] . '%');
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

        $imageUpload = $this->uploadSingleImage($request, 'image', 'staff', 'staff', 550, 728);

        DB::beginTransaction();
        try {
            $this->staff
                ->create([
                    'name' => $data['name'],
                    'position' => $data['position'],
                    'link_linked_in' => $data['link_linked_in'] ?? '',
                    'link_facebook' => $data['link_facebook'] ?? '',
                    'link_instagram' => $data['link_instagram'] ?? '',
                    'link_youtube' => $data['link_youtube'] ?? '',
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
            'message' => 'Thêm Nhân viên thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $staff = $this->staff
            ->find($id);

        if (!$staff) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Nhân viên không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $staff
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $staff = $this->staff
            ->find($data['id']);

        if (!$staff) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Nhân viên không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $staff->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'staff', 'staff', 500, 728);
        } else {
            $imageUpload['image_path'] = $staff->image_path;
            $imageUpload['image_name'] = $staff->image_name;
        }

        DB::beginTransaction();
        try {
            $staff
                ->update([
                    'name' => $data['name'],
                    'position' => $data['position'],
                    'link_linked_in' => $data['link_linked_in'] ?? '',
                    'link_facebook' => $data['link_facebook'] ?? '',
                    'link_instagram' => $data['link_instagram'] ?? '',
                    'link_youtube' => $data['link_youtube'] ?? '',
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
            'message' => 'Sửa Nhân viên thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $staff = $this->staff
            ->find($id);

        if (!$staff) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Nhân viên không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $staff->image_path;

        DB::beginTransaction();
        try {
            if ($staff->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Nhân viên không thành công'
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
            'message' => 'Xoá Nhân viên thành công'
        ]);
    }
}
