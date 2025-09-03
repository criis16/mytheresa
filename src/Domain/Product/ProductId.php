<?php

namespace App\Domain\Product;

class ProductId
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Return the product id value
     *
     * @return integer
     */
    public function getValue(): int
    {
        return $this->id;
    }
}
