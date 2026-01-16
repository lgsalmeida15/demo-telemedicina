<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AutoridadeRepository;
use App\Models\Autoridade;
use App\Validators\AutoridadeValidator;

/**
 * Class AutoridadeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AutoridadeRepositoryEloquent extends BaseRepository implements AutoridadeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Autoridade::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
