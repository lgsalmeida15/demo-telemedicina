<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ConvenioTypeRepository;
use App\Models\ConvenioType;
use App\Validators\ConvenioTypeValidator;

/**
 * Class ConvenioTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ConvenioTypeRepositoryEloquent extends BaseRepository implements ConvenioTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ConvenioType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
