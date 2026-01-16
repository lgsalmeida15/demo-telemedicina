<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ConveniosCategoriaRepository;
use App\Models\ConveniosCategoria;
use App\Validators\ConveniosCategoriaValidator;

/**
 * Class ConveniosCategoriaRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ConveniosCategoriaRepositoryEloquent extends BaseRepository implements ConveniosCategoriaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ConveniosCategoria::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
