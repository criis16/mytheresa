<?php

namespace App\Domain\Product;

class ProductName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Return the product name value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->name;
    }
}
