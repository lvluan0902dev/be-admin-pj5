<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Repositories\BaseRepository;
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
     * @var BaseRepository
     */
    private  $baseRepository;

    public function __construct(
        Faq $faq,
        BaseRepository $baseRepository
    )
    {
        $this->faq = $faq;
        $this->baseRepository = $baseRepository;
    }
}
