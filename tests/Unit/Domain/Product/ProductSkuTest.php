<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\ProductSku;
use PHPUnit\Framework\TestCase;

class ProductSkuTest extends TestCase
{
    private ProductSku $sut;

    public function testGetValue(): void
    {
        $this->sut = new ProductSku("000001");

        $this->assertEquals(1, $this->sut->getValue());
    }
}
