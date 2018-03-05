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
       //  $model = $model->whereNull('video_data')->limit(1);
       // $model = $model->whereNull('subtitle')->limit(1);
        // $model = $model->where(['s1' => 0])->limit(1000);
        $model = $model->limit(1000)->offset(6000);
        return $model;
    }
}
