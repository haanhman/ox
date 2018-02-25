<?php

namespace App\Presenters;

use App\Transformers\YoutubeTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class YoutubePresenter.
 *
 * @package namespace App\Presenters;
 */
class YoutubePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new YoutubeTransformer();
    }
}
