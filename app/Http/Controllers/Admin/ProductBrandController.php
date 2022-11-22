<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductBrandController extends Controller
{
    use ResponseTrait;

    /**
     * @var ProductBrand
     */
    private $productBrand;

    /**
     * @var BaseRepository
     */
    private  $baseRepository;

    /**
     * ProductBrandController constructor.
     * @param ProductBrand $productBrand
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        ProductBrand $productBrand,
        BaseRepository $baseRepository
    )
    {
        $this->productBrand = $productBrand;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->productBrand;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->all();

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $this->productBrand
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
            'message' => 'Thêm Thương hiệu sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $productBrand = $this->productBrand
            ->find($id);

        if (!$productBrand) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thương hiệu sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $productBrand
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $productBrand = $this->productBrand
            ->find($data['id']);

        if (!$productBrand) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thương hiệu sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $productBrand
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
            'message' => 'Thương hiệu sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $productBrand = $this->productBrand
            ->find($id);

        if (!$productBrand) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thương hiệu sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($productBrand->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Thương hiệu sản phẩm không thành công'
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
            'message' => 'Xoá Thương hiệu sản phẩm thành công'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $productBrands = $this->productBrand
            ->orderBy('name', 'ASC')
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $productBrands
        ]);
    }
}
