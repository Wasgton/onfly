<?php

namespace App\Repository\Contracts;

use App\Models\TravelOrder;

interface TravelOrderRepository
{
    public function store(array $data) : TravelOrder;

    public function getPaginated();
}