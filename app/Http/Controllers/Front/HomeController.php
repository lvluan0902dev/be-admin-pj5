<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BeautyImage;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Traits\ResponseTrait;
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
     * HomeController constructor.
     * @param Slider $slider
     * @param Testimonial $testimonial
     */
    public function __construct(
        Slider $slider,
        Testimonial $testimonial,
        BeautyImage $beautyImage
    )
    {
        $this->slider = $slider;
        $this->testimonial = $testimonial;
        $this->beautyImage = $beautyImage;
    }

    public function getAllSlider()
    {
        $sliders = $this->slider
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $sliders
        ]);
    }

    public function getAllTestimonial()
    {
        $testimonials = $this->testimonial
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $testimonials
        ]);
    }

    public function getAllBeautyImage()
    {
        $beautyImages = $this->beautyImage
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $beautyImages
        ]);
    }
}