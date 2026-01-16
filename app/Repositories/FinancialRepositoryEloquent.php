<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FinancialRepository;
use App\Models\Financial;
use App\Validators\FinancialValidator;

/**
 * Class FinancialRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FinancialRepositoryEloquent extends BaseRepository implements FinancialRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Financial::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
