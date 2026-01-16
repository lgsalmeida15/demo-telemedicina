<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ContaPagarRepository;
use App\Models\ContaPagar;
use App\Validators\ContaPagarValidator;

/**
 * Class ContaPagarRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ContaPagarRepositoryEloquent extends BaseRepository implements ContaPagarRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ContaPagar::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
