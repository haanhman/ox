<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\WordType;

/**
 * Class WordTypeTransformer.
 *
 * @package namespace App\Transformers;
 */
class WordTypeTransformer extends TransformerAbstract
{
    /**
     * Transform the WordType entity.
     *
     * @param \App\Entities\WordType $model
     *
     * @return array
     */
    public function transform(WordType $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
