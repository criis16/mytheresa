<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\ProductId;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    private ProductId $sut;

    public function testGetValue(): void
    {
        $this->sut = new ProductId(1);

        $this->assertEquals(1, $this->sut->getValue());
    }
}
