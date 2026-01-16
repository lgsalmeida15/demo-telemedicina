<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PartnerCompanyRepository;
use App\Models\PartnerCompany;
use App\Validators\PartnerCompanyValidator;

/**
 * Class PartnerCompanyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PartnerCompanyRepositoryEloquent extends BaseRepository implements PartnerCompanyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PartnerCompany::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
