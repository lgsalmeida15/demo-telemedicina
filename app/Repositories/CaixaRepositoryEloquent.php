<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CaixaRepository;
use App\Models\Caixa;
use App\Validators\CaixaValidator;

/**
 * Class CaixaRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CaixaRepositoryEloquent extends BaseRepository implements CaixaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Caixa::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
