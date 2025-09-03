<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\ProductCategory;
use PHPUnit\Framework\TestCase;

class ProductCategoryTest extends TestCase
{
    private ProductCategory $sut;

    public function testGetValue(): void
    {
        $productCategory = "a product category name";
        $this->sut = new ProductCategory($productCategory);

        $this->assertEquals($productCategory, $this->sut->getValue());
    }
}
