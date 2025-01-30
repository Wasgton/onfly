<?php

namespace App\Repository;

use App\Models\TravelOrder;
use App\Repository\Contracts\TravelOrderRepository;
use Nette\NotImplementedException;

class TravelOrderRepositoryEloquent implements TravelOrderRepository 
{

    public function store(array $data): TravelOrder
    {
        return TravelOrder::create($data);
    }

    public function getPaginated($filters = [], $limit = 15)
    {
        $query = TravelOrder::query();        
        if (isset($filters['create_period_start'], $filters['create_period_end'])) {
            $query->where('created_at', '>=', $filters['create_period_start'])
                ->where('created_at', '<=', $filters['create_period_end']);                   
        }
        if (isset($filters['departure_period_start'], $filters['departure_period_end'])){
            $query->where('departure_date', '>=', $filters['departure_period_start'])
                ->where('departure_date', '<=', $filters['departure_period_end']);
        }
        if (isset($filters['destination'])) {
            $query->where('destination', 'like', "%{$filters['destination']}%");
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['applicant_name'])) {
            $query->where('applicant_name', 'like', "%{$filters['applicant_name']}%");
        }
        return $query->paginate($limit);
    }
}