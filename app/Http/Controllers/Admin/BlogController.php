<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use App\Traits\UploadImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    use ResponseTrait;
    use UploadImageTrait;

    /**
     * @var Blog
     */
    private $blog;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * BlogController constructor.
     * @param Blog $blog
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Blog $blog,
        BaseRepository $baseRepository
    )
    {
        $this->blog = $blog;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->blog->with(['blog_category']);

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('title', 'LIKE', '%' . $params['search'] . '%');
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

        $imageUpload = $this->uploadSingleImage($request, 'image', 'blog', 'blog', 540, 350);

        DB::beginTransaction();
        try {
            $this->blog
                ->create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'blog_category_id' => $data['blog_category_id'],
                    'view_count' => 0,
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
            'message' => 'Thêm Bài viết thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function get($id)
    {
        $blog = $this->blog->with(['blog_category'])
            ->find($id);

        if (!$blog) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $blog
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $blog = $this->blog
            ->find($data['id']);

        if (!$blog) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        $imageUpload = array();

        $imagePathOld = $blog->image_path;

        if ($request->file('image')) {
            $imageUpload = $this->uploadSingleImage($request, 'image', 'blog', 'blog', 540, 350);
        } else {
            $imageUpload['image_path'] = $blog->image_path;
            $imageUpload['image_name'] = $blog->image_name;
        }

        DB::beginTransaction();
        try {
            $blog
                ->update([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'image_name' => $imageUpload['image_name'],
                    'image_path' => $imageUpload['image_path'],
                    'blog_category_id' => $data['blog_category_id'],
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
            'message' => 'Sửa Bài viết thành công'
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $blog = $this->blog
            ->find($id);

        if (!$blog) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        $imagePath = $blog->image_path;

        DB::beginTransaction();
        try {
            if ($blog->delete()) {
                $this->deleteImage($imagePath);
            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Bài viết không thành công'
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
            'message' => 'Xoá Bài viết thành công'
        ]);
    }
}
