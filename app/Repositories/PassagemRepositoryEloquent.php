<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PassagemRepository;
use App\Models\Passagem;
use App\Validators\PassagemValidator;

/**
 * Class PassagemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PassagemRepositoryEloquent extends BaseRepository implements PassagemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Passagem::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
