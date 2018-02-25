<?php

namespace App\Presenters;

use App\Transformers\WordTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class WordPresenter.
 *
 * @package namespace App\Presenters;
 */
class WordPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new WordTransformer();
    }
}
