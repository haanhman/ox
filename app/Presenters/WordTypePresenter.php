<?php

namespace App\Presenters;

use App\Transformers\WordTypeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class WordTypePresenter.
 *
 * @package namespace App\Presenters;
 */
class WordTypePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new WordTypeTransformer();
    }
}
