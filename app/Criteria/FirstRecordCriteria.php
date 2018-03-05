<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FirstRecordCriteria.
 *
 * @package namespace App\Criteria;
 */
class FirstRecordCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->whereNull('video_data')->limit(1);
//        $model->whereNotNull('video_data');
//        $model = $model->whereNotNull('video_data')->where(['is_ok' => 0])->limit(500);
        return $model;
    }
}
