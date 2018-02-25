<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Word;

/**
 * Class WordTransformer.
 *
 * @package namespace App\Transformers;
 */
class WordTransformer extends TransformerAbstract
{
    /**
     * Transform the Word entity.
     *
     * @param \App\Entities\Word $model
     *
     * @return array
     */
    public function transform(Word $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
