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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function shop(Request $request)
    {
        $query = $this->product
            ->with(['product_images', 'product_category', 'product_options'])
            ->where('status', Product::ACTIVE_STATUS);

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('name', 'LIKE', '%' . $params['search'] . '%');
        }

        // Filter
        if (isset($params['product_brand_id']) && !empty($params['product_brand_id']) && $params['product_brand_id'] != 0) {
            $query = $query
                ->where('product_brand_id', $params['product_brand_id']);
        }
        if (isset($params['product_category_id']) && !empty($params['product_category_id']) && $params['product_category_id'] != 0) {
            $query = $query
                ->where('product_category_id', $params['product_category_id']);
        }


        // Sort
        if (isset($params['sort_by_price_type']) && !empty($params['sort_by_price_type']) && $params['sort_by_price_type'] != 0) {
            $sortType = $params['sort_by_price_type'] == 1 ? 'ASC' : 'DESC';
            $query = $query->orderBy('option_price', $sortType);
        } else {
            $query = $this->baseRepository->sort($query, $params);
        }

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
     * @param $url
     * @return JsonResponse
     */
    public function getProduct($url)
    {
        $product = $this->product
            ->where('url', $url)
            ->where('status', Product::ACTIVE_STATUS)
            ->first();

        if (!$product) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $product->update([
            'view_count' => $product->view_count + 1
        ]);

        $product = $this->product
            ->with(['product_images', 'product_options', 'product_category', 'product_brand'])
            ->find($product->id);

        return $this->responseJson([
            'success' => 1,
            'data' => $product
        ]);
    }

    /**
     * @param $id - product id
     */
    public function getProductsRelated($url)
    {
        $product = $this->product
            ->where('url', $url)
            ->first();

        $productsRelated = $this->product
            ->with(['product_category'])
            ->where('product_category_id', $product->product_category_id)
            ->orWhere('product_brand_id', $product->product_brand_id)
            ->take(4)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $productsRelated
        ]);
    }
}
