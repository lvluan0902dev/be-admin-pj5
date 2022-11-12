<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    use ResponseTrait;

    /**
     * @var Slider
     */
    private $slider;

    /**
     * SliderController constructor.
     * @param Slider $slider
     */
    public function __construct(
        Slider $slider
    )
    {
        $this->slider = $slider;
    }

    public function getAll()
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
}
