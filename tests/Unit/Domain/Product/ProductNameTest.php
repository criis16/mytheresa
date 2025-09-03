<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\ProductName;
use PHPUnit\Framework\TestCase;

class ProductNameTest extends TestCase
{
    private ProductName $sut;

    public function testGetValue(): void
    {
        $productName = "a product name";
        $this->sut = new ProductName($productName);

        $this->assertEquals($productName, $this->sut->getValue());
    }
}
