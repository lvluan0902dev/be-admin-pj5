<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use UploadImageTrait;
    use ResponseTrait;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductImage
     */
    private $productImage;

    /**
     * @var ProductOption
     */
    private $productOption;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * ProductController constructor.
     * @param Product $product
     * @param ProductImage $productImage
     * @param ProductOption $productOption
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Product $product,
        ProductImage $productImage,
        ProductOption $productOption,
        BaseRepository $baseRepository
    )
    {
        $this->product = $product;
        $this->productImage = $productImage;
        $this->productOption = $productOption;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->product->with(['product_category', 'product_brand']);

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

        $imageUpload = $this->uploadSingleImage($request, 'image', 'product', 'product', 540, 720);

        DB::beginTransaction();
        try {
            $this->product
                ->create([
                    'product_category_id' => $data['product_category_id'],
                    'product_brand_id' => $data['product_brand_id'],
                    'name' => $data['name'],
                    'short_description' => $data['short_description'] ?? '',
                    'product_detail' => $data['product_detail'] ?? '',
                    'how_to_use' => $data['how_to_use'] ?? '',
                    'ingredients' => $data['ingredients'] ?? '',
                    'view_count' => 0,
                    'order_count' => 0,
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
            'message' => 'Thêm Sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $product = $this->product
            ->with(['product_category', 'product_brand', 'product_options', 'product_images'])
            ->find($id);

        if (!$product) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $product
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $product = $this->product
            ->find($data['id']);

        if (!$product) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $product->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'product', 'product', 540, 720);
        } else {
            $imageUpload['image_path'] = $product->image_path;
            $imageUpload['image_name'] = $product->image_name;
        }

        DB::beginTransaction();
        try {
            $product
                ->update([
                    'product_category_id' => $data['product_category_id'],
                    'product_brand_id' => $data['product_brand_id'],
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'product_detail' => $data['product_detail'],
                    'how_to_use' => $data['how_to_use'],
                    'ingredients' => $data['ingredients'],
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
            'message' => 'Sửa Sản phẩm thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $product = $this->product
            ->find($id);

        if (!$product) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $product->image_path;

        DB::beginTransaction();
        try {
            if ($product->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Sản phẩm không thành công'
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
            'message' => 'Xoá Sản phẩm thành công'
        ]);
    }

    /**
     * @param $id - product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProductImage($id, Request $request)
    {
        $imageUpload = $this->uploadSingleImage($request, 'image', 'product-image', 'product-image', 540, 720);

        DB::beginTransaction();
        try {
            $this->productImage
                ->create([
                    'product_id' => $id,
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path']
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
            'message' => 'Thêm Hình ảnh sản phẩm thành công'
        ]);
    }

    /**
     * @param $id - product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productImageList($id, Request $request)
    {
        $query = $this->productImage
            ->where('product_id', $id);

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
     * @param $id - image_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productImageDelete($id)
    {
        $productImage = $this->productImage
            ->find($id);

        if (!$productImage) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Hình ảnh sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $productImage->image_path;

        DB::beginTransaction();
        try {
            if ($productImage->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Hình ảnh phẩm không thành công'
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
            'message' => 'Xoá Hình ảnh sản phẩm thành công'
        ]);
    }

    /**
     * @param $id - product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOptionList($id, Request $request)
    {
        $query = $this->productOption
            ->where('product_id', $id);

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
     * @param $id - product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOptionAdd($id, Request $request)
    {
        $data = $request->all();

        DB::beginTransaction();
        try {
            $this->productOption
                ->create([
                    'product_id' => $id,
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'stock' => $data['stock']
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
            'message' => 'Thêm Thuộc tính sản phẩm thành công'
        ]);
    }

    /**
     * @param $id - product_option_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOptionDelete($id)
    {
        $productOption = $this->productOption
            ->find($id);

        if (!$productOption) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thuộc tính sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($productOption->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Thuộc tính sản phẩm không thành công'
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
            'message' => 'Thuộc tính sản phẩm sản phẩm thành công'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOptionEdit(Request $request)
    {
        $data = $request->all();

        $productOption = $this->productOption
            ->find($data['id']);

        if (!$productOption) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thuộc tính sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            $productOption
                ->update([
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'stock' => $data['stock']
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
            'message' => 'Sửa Thuộc tính sản phẩm thành công'
        ]);
    }

    /**
     * @param $id - product_option_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOptionGet($id)
    {
        $productOption = $this->productOption
            ->find($id);

        if (!$productOption) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Thuộc tính sản phẩm không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $productOption
        ]);
    }
}
