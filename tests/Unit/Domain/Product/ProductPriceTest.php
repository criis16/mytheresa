<?php

namespace Tests\Unit\Domain\Product;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductPrice;

class ProductPriceTest extends TestCase
{
    private ProductPrice $sut;

    public function testGetValue(): void
    {
        $inputPrice = 10000; // Price in cents
        $expectedPrice = 100.00; // Expected price in dollars
        $this->sut = new ProductPrice($inputPrice);

        $this->assertEquals($expectedPrice, $this->sut->getValue());
    }

    public function testNegativePriceThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price cannot be negative.');

        $inputPrice = -10000; // Negative price in cents
        $this->sut = new ProductPrice($inputPrice);
        $this->sut->getValue();
    }
}
