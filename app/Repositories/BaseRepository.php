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
            $sortField = $params['sort_field'];
        } else {
            $sortField = 'created_at';
        }

        if (isset($params['sort_type']) && !empty($params['sort_type'])) {
            $sortType = $params['sort_type'];
        } else {
            $sortType = 'DESC';
        }

        $query = $query->orderBy($sortField, $sortType);

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
            $perPage = $params['per_page'];
        } else {
            $perPage = 10;
        }

        if (isset($params['first_row']) && !empty($params['first_row'])) {
            $firstRow = $params['first_row'];
        } else {
            $firstRow = 0;
        }

        $page = $firstRow / $perPage + 1;

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $data,
            'page' => $page,
            'per_page' => $perPage
        ];
    }

    /**
     * @param $statusRaw
     * @return int
     */
    public function convertStatus($statusRaw)
    {
        if ($statusRaw == 'true') {
            $statusResult = 1;
        } else {
            $statusResult = 0;
        }

        return $statusResult;
    }
}
