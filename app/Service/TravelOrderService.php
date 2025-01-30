<?php

namespace App\Service;

use App\Exceptions\FailToCreateException;
use App\Models\TravelOrder;
use App\Repository\Contracts\TravelOrderRepository;

class TravelOrderService
{
    public function __construct(private TravelOrderRepository $repository) {}

    /**
     * @throws FailToCreateException
     */
    public function store(array $data): TravelOrder
    {
        $data['user_id'] = auth()->id();
        $data['applicant_name'] = auth()->user()->name; 
        if (! $travelOrder = $this->repository->store($data)) {
            throw new FailToCreateException('Error creating travel order', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $travelOrder;
    }

    public function getPaginated(array $filters)
    {
       $limit = isset($filters['per_page']) ? min($filters['per_page'], 100) : 15;
       return $this->repository->getPaginated($filters, $limit);
    }
    
}
