<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AutorizacaoCompraRepository;
use App\Models\AutorizacaoCompra;
use App\Validators\AutorizacaoCompraValidator;

/**
 * Class AutorizacaoCompraRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AutorizacaoCompraRepositoryEloquent extends BaseRepository implements AutorizacaoCompraRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AutorizacaoCompra::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
