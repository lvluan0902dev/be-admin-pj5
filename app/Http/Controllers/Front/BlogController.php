<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ResponseTrait;

    /**
     * @var Blog
     */
    private $blog;

    /**
     * @var BlogCategory
     */
    private $blogCategory;

    /**
     * @var BaseRepository
     */
    private $baseRepository;

    /**
     * BlogController constructor.
     * @param Blog $blog
     * @param BlogCategory $blogCategory
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Blog $blog,
        BlogCategory $blogCategory,
        BaseRepository $baseRepository
    )
    {
        $this->blog = $blog;
        $this->blogCategory = $blogCategory;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAllBlogCategory()
    {
        $blogCategories = $this->blogCategory
            ->with(['blogs' => function ($query) {
                $query->where('status', Blog::ACTIVE_STATUS);
            }])
            ->where('status', BlogCategory::ACTIVE_STATUS)
            ->orderBy('name', 'ASC')
            ->get();

        foreach ($blogCategories as $blogCategory) {
            $blogCategory->blogCount = count($blogCategory->blogs);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $blogCategories
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function blog(Request $request)
    {
        $query = $this->blog
            ->with(['blog_category'])
            ->where('status', Blog::ACTIVE_STATUS);

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('title', 'LIKE', '%' . $params['search'] . '%');
        }

        // Filter
        if (isset($params['blog_category_id']) && !empty($params['blog_category_id']) && $params['blog_category_id'] != 0) {
            $query = $query
                ->where('blog_category_id', $params['blog_category_id']);
        }


        // Sort

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
    public function getBlog($url)
    {
        $blog = $this->blog
            ->where('url', $url)
            ->where('status', Blog::ACTIVE_STATUS)
            ->first();

        if (!$blog) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Bài viết không tồn tại hoặc đã bị xoá'
            ]);
        }

        $blog->update([
            'view_count' => $blog->view_count + 1
        ]);

        $blog = $this->blog
            ->with(['blog_category'])
            ->find($blog->id);

        return $this->responseJson([
            'success' => 1,
            'data' => $blog
        ]);
    }
}
