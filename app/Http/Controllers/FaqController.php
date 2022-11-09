<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Repositories\BaseRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * FaqController constructor.
     * @param Faq $faq
     * @param BaseRepository $baseRepository
     */
    public function __construct(
        Faq $faq,
        BaseRepository $baseRepository
    )
    {
        $this->faq = $faq;
        $this->baseRepository = $baseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = $this->faq;

        $params = $request->all();

        $total = $query->count();

        // Search
        if (isset($params['search']) && !empty($params['search'])) {
            $query = $query
                ->where('title', 'LIKE', '%' . $params['search'] . '%')
                ->orWhere('content', 'LIKE', '%' . $params['search'] . '%');
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

        DB::beginTransaction();
        try {
            $this->faq
                ->create([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'status' => $data['status']
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
            'message' => 'Thêm Câu hỏi thường gặp thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $faq = $this->faq
            ->find($id);

        if (!$faq) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Câu hỏi thường gặp không tồn tại hoặc đã bị xoá'
            ]);
        }

        return $this->responseJson([
            'success' => 1,
            'data' => $faq
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();

        $faq = $this->faq
            ->find($data['id']);

        if (!$faq) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Câu hỏi thường gặp không tồn tại hoặc đã bị xoá'
            ]);
        }

        $data['status'] = $this->baseRepository->convertStatus($data['status']);

        DB::beginTransaction();
        try {
            $faq
                ->update([
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'status' => $data['status']
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
            'message' => 'Sửa Câu hỏi thường gặp thành công'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $faq = $this->faq
            ->find($id);

        if (!$faq) {
            return $this->responseJson([
                'success' => 0,
                'message' => 'Câu hỏi thường gặp không tồn tại hoặc đã bị xoá'
            ]);
        }

        DB::beginTransaction();
        try {
            if ($faq->delete()) {

            } else {
                return $this->responseJson([
                    'success' => 0,
                    'message' => 'Xoá Câu hỏi thường gặp không thành công'
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
            'message' => 'Xoá Câu hỏi thường gặp thành công'
        ]);
    }
}
