<?php

namespace App\Domain\Product;

class ProductSku
{
    private string $sku;

    public function __construct(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * Return the product sku value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->sku;
    }
}
