<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\YoutubeRepository;
use App\Entities\Youtube;
use App\Validators\YoutubeValidator;

/**
 * Class YoutubeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class YoutubeRepositoryEloquent extends BaseRepository implements YoutubeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Youtube::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
