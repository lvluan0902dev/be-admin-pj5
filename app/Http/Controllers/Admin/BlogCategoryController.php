<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogCategoryController extends Controller
{
    use ResponseTrait;

    /**
     * @var BlogCategory
     */
    private $blogCategory;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * BlogCategoryController constructor.
     * @param BlogCategory $blogCategory
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        BlogCategory $blogCategory,
        BaseRepository $baseRepository
    )
    {
        $this->blogCategory = $blogCategory;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->blogCategory;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('name', 'LIKE', '%' . $params['search'] . '%');
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

        DB::beginTransaction();
        try {
            $this->blogCategory
                ->create([
                    'name' => $data['name'],
                    'status' => $data['status']
                ]);
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
            'message' => 'Thêm Danh mục bài viết thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get($id)
    {
        $blogCategory = $this->blogCategory
            ->find($id);

        if (!$blogCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $blogCategory
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $blogCategory = $this->blogCategory
            ->find($data['id']);

        if (!$blogCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $blogCategory
                ->update([
                    'name' => $data['name'],
                    'status' => $data['status']
                ]);
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
            'message' => 'Sửa Danh mục bài viết thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $blogCategory = $this->blogCategory
            ->find($id);

        if (!$blogCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($blogCategory->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Danh mục bài viết không thành công'
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
            'message' => 'Xoá Danh mục bài viết thành công'
        ]);
    }
}
