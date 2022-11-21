<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use ResponseTrait;

    /**
     * @var Faq
     */
    private $faq;

    /**
     * FaqController constructor.
     * @param Faq $faq
     */
    public function __construct(
        Faq $faq
    )
    {
        $this->faq = $faq;
    }

    public function getAllFaq() {
        $faqs = $this->faq
            ->where('status', 1)
            ->latest()
            ->get();

        return $this->responseJson([
            'success' => 1,
            'data' => $faqs
        ]);
    }
}
