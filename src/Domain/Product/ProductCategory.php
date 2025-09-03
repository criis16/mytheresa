<?php

namespace App\Domain\Product;

class ProductCategory
{
    private string $category;

    public function __construct(string $category)
    {
        $this->category = $category;
    }

    /**
     * Return the product category value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->category;
    }
}
