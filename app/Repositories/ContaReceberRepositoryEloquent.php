<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ContaReceberRepository;
use App\Models\ContaReceber;
use App\Validators\ContaReceberValidator;

/**
 * Class ContaReceberRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ContaReceberRepositoryEloquent extends BaseRepository implements ContaReceberRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ContaReceber::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
