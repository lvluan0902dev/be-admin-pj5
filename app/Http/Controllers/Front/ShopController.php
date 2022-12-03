<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    use ResponseTrait;

    /**
     * @var ProductCategory
     */
    private $productCategory;

    /**
     * @var ProductBrand
     */
    private $productBrand;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * ShopController constructor.
     * @param ProductCategory $productCategory
     * @param ProductBrand $productBrand
     * @param Product $product
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        ProductCategory $productCategory,
        ProductBrand $productBrand,
        Product $product,
        BaseRepository $baseRepository
    )
    {
        $this->productCategory = $productCategory;
        $this->productBrand = $productBrand;
        $this->product = $product;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAllProductCategory()
    {
        $productCategories = $this->productCategory
            ->with(['products' => function ($query) {
                $query->where('status', Product::ACTIVE_STATUS);
            }])
            ->where('status', ProductCategory::ACTIVE_STATUS)
            ->orderBy('name', 'ASC')
            ->get();

        foreach ($productCategories as $productCategory) {
            $productCategory->productCount = count($productCategory->products);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $productCategories
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAllProductBrand()
    {
        $productBrands = $this->productBrand
            ->with(['products' => function ($query) {
                $query->where('status', Product::ACTIVE_STATUS);
            }])
            ->where('status', ProductBrand::ACTIVE_STATUS)
            ->orderBy('name', 'ASC')
            ->get();

        foreach ($productBrands as $productBrand) {
            $productBrand->productCount = count($productBrand->products);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $productBrands
        ]);
    }

    public function shop(Request $request)
    {
        $query = $this->product
            ->with(['product_images', 'product_category', 'product_options']);

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
}
