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
    
}