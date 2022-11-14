<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
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
     * HomeController constructor.
     * @param Slider $slider
     * @param Testimonial $testimonial
     */
    public function __construct(
        Slider $slider,
        Testimonial $testimonial
    )
    {
        $this->slider = $slider;
        $this->testimonial = $testimonial;
    }

    public function getAllSlider() {
        $sliders = $this->slider
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $sliders
        ]);
    }

    public function getAllTestimonial() {
        $testimonials = $this->testimonial
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $testimonials
        ]);
    }
}
