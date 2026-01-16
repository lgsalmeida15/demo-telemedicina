<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\InvoiceHistoryRepository;
use App\Models\InvoiceHistory;
use App\Validators\InvoiceHistoryValidator;

/**
 * Class InvoiceHistoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class InvoiceHistoryRepositoryEloquent extends BaseRepository implements InvoiceHistoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return InvoiceHistory::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
