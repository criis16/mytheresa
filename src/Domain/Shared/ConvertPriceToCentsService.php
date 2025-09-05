<?php

namespace App\Domain\Shared;

class ConvertPriceToCentsService
{
    /**
     * Convert price to cents
     *
     * @param float $price
     * @return integer
     */
    public function execute(float $price): int
    {
        return (int) \round($price * 100);
    }
}
