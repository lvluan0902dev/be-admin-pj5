<?php

namespace App\Repositories;

class BaseRepository
{
    /**
     * @param $query
     * @param $params
     * @return mixed
     */
    public function sort($query, $params)
    {
        if (isset($params['sort_field']) && !empty($params['sort_field'])) {
            $sort_field = $params['sort_field'];
        } else {
            $sort_field = 'created_at';
        }

        if (isset($params['sort_type']) && !empty($params['sort_type'])) {
            $sort_type = $params['sort_type'];
        } else {
            $sort_type = 'DESC';
        }

        $query = $query->orderBy($sort_field, $sort_type);

        return $query;
    }

    /**
     * @param $query
     * @param $params
     * @return array
     */
    public function paginate($query, $params)
    {
        if (isset($params['per_page']) && !empty($params['per_page'])) {
            $per_page = $params['per_page'];
        } else {
            $per_page = 10;
        }

        if (isset($params['first_row']) && !empty($params['first_row'])) {
            $first_row = $params['first_row'];
        } else {
            $first_row = 0;
        }

        $page = $first_row / $per_page + 1;

        $data = $query->paginate($per_page, ['*'], 'page', $page);

        return [
            'data' => $data,
            'page' => $page,
            'per_page' => $per_page
        ];
    }

    /**
     * @param $status_raw
     * @return int
     */
    public function convertStatus($status_raw)
    {
        if ($status_raw == 'true') {
            $status_result = 1;
        } else {
            $status_result = 0;
        }

        return $status_result;
    }
}
