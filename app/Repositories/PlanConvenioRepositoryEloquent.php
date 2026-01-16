<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PlanConvenioRepository;
use App\Models\PlanConvenio;
use App\Validators\PlanConvenioValidator;

/**
 * Class PlanConvenioRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PlanConvenioRepositoryEloquent extends BaseRepository implements PlanConvenioRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PlanConvenio::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
