<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BeneficiaryRepository;
use App\Models\Beneficiary;
use App\Validators\BeneficiaryValidator;

/**
 * Class BeneficiaryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BeneficiaryRepositoryEloquent extends BaseRepository implements BeneficiaryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Beneficiary::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
