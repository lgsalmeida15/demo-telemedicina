<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\DependentRepository;
use App\Models\Dependent;
use App\Validators\DependentValidator;

/**
 * Class DependentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DependentRepositoryEloquent extends BaseRepository implements DependentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Dependent::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
