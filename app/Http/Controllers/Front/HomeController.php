<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BeautyImage;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ResponseTrait;

    /**
     * @var Slider
     */
    private $slider;

    /**
     * @var Testimonial
     */
    private $testimonial;

    /**
     * @var BeautyImage
     */
    private $beautyImage;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Blog
     */
    private $blog;

    /**
     * HomeController constructor.
     * @param Slider $slider
     * @param Testimonial $testimonial
     * @param BeautyImage $beautyImage
     * @param Product $product
     * @param Blog $blog
     */
    public function __construct(
        Slider $slider,
        Testimonial $testimonial,
        BeautyImage $beautyImage,
        Product $product,
        Blog $blog
    )
    {
        $this->slider = $slider;
        $this->testimonial = $testimonial;
        $this->beautyImage = $beautyImage;
        $this->product = $product;
        $this->blog = $blog;
    }

    /**
     * @return JsonResponse
     */
    public function getAllSlider()
    {
        $sliders = $this->slider
            ->where('status', Slider::ACTIVE_STATUS)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $sliders
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAllTestimonial()
    {
        $testimonials = $this->testimonial
            ->where('status', Testimonial::ACTIVE_STATUS)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $testimonials
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getAllBeautyImage()
    {
        $beautyImages = $this->beautyImage
            ->where('status', BeautyImage::ACTIVE_STATUS)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $beautyImages
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getProductsLatest()
    {
        $products = $this->product
            ->with(['product_options', 'product_images', 'product_category'])
            ->where('status', 1)
            ->latest()
            ->take(5)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $products
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getMostViewedProducts()
    {
        $products = $this->product
            ->with(['product_options', 'product_images', 'product_category'])
            ->where('status', 1)
            ->orderBy('view_count', 'DESC')
            ->take(6)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $products
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getSomeBlogs()
    {
        $blogs = $this->blog
            ->with(['blog_category'])
            ->where('status', Blog::ACTIVE_STATUS)
            ->latest()
            ->take(5)
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $blogs
        ]);
    }
}
