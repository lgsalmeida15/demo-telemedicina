<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CartaoCreditoRepository;
use App\Models\CartaoCredito;
use App\Validators\CartaoCreditoValidator;

/**
 * Class CartaoCreditoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CartaoCreditoRepositoryEloquent extends BaseRepository implements CartaoCreditoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CartaoCredito::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
