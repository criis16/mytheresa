<?php

namespace App\Domain\Product;

use InvalidArgumentException;

class ProductPrice
{
    private float $price;

    public function __construct(int $price)
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }

        $this->price = $this->convertPriceToFloat($price);
    }

    /**
     * Return the product price value
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->price;
    }

    private function convertPriceToFloat(int $price): float
    {
        return $price / 100;
    }
}
