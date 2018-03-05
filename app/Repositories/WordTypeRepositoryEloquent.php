<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WordTypeRepository;
use App\Entities\WordType;
use App\Validators\WordTypeValidator;

/**
 * Class WordTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WordTypeRepositoryEloquent extends BaseRepository implements WordTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WordType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
