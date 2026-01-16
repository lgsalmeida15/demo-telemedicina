<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AssociacaoRepository;
use App\Models\Associacao;
use App\Validators\AssociacaoValidator;

/**
 * Class AssociacaoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AssociacaoRepositoryEloquent extends BaseRepository implements AssociacaoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Associacao::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
