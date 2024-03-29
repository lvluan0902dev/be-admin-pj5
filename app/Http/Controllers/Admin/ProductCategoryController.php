<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    use ResponseTrait;

    /**
     * @var ProductCategory
     */
    private $productCategory;

    /**
     * @var BaseRepository
     */
    private  $baseRepository;

    /**
     * ProductCategoryController constructor.
     * @param ProductCategory $productCategory
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        ProductCategory $productCategory,
        BaseRepository $baseRepository
    )
    {
        $this->productCategory = $productCategory;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->productCategory;

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
            $this->productCategory
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
            'message' => 'Thêm Danh mục sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get($id)
    {
        $productCategory = $this->productCategory
            ->find($id);

        if (!$productCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $productCategory
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $productCategory = $this->productCategory
            ->find($data['id']);

        if (!$productCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $productCategory
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
            'message' => 'Danh mục sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $productCategory = $this->productCategory
            ->find($id);

        if (!$productCategory) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Danh mục sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($productCategory->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Danh mục sản phẩm không thành công'
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
            'message' => 'Xoá Danh mục sản phẩm thành công'
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        $productCategories = $this->productCategory
            ->orderBy('name', 'ASC')
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $productCategories
        ]);
    }
}
