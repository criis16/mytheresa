<?php

namespace Tests\Unit\Application\Product\GetCurrentPrice;

use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetCurrentPriceServiceTest extends TestCase
{
    private GetCurrentPriceService $sut;

    protected function setUp(): void
    {
        $this->sut = new GetCurrentPriceService();
    }

    public function testExecuteWithDiscounts(): void
    {
        $skuValue = '000003';
        $categoryValue = 'boots';
        $priceValue = 100.55;
        $discountPercentage = '30%';
        $expectedOriginalPrice = 10055;
        $expectedFinalPrice = 7039;

        /** @var ProductSku&MockObject */
        $productSku = $this->createMock(ProductSku::class);
        $productSku->expects(self::once())
            ->method('getValue')
            ->willReturn($skuValue);

        /** @var ProductCategory&MockObject */
        $productCategory = $this->createMock(ProductCategory::class);
        $productCategory->expects(self::once())
            ->method('getValue')
            ->willReturn($categoryValue);

        /** @var ProductPrice&MockObject */
        $productPrice = $this->createMock(ProductPrice::class);
        $productPrice->expects(self::once())
            ->method('getValue')
            ->willReturn($priceValue);

        /** @var Product&MockObject */
        $product = $this->createMock(Product::class);
        $product->expects(self::once())
            ->method('getSku')
            ->willReturn($productSku);
        $product->expects(self::once())
            ->method('getCategory')
            ->willReturn($productCategory);
        $product->expects(self::once())
            ->method('getPrice')
            ->willReturn($productPrice);

        $this->assertEquals(
            [
                'original' => $expectedOriginalPrice,
                'final' => $expectedFinalPrice,
                'discount_percentage' => $discountPercentage,
                'currency' => 'EUR'
            ],
            $this->sut->execute($product)
        );
    }

    public function testExecuteWithoutDiscounts(): void
    {
        $skuValue = '000002';
        $categoryValue = 'sandals';
        $priceValue = 100.55;
        $discountPercentage = null;
        $expectedFinalPrice = $expectedOriginalPrice = 10055;

        /** @var ProductSku&MockObject */
        $productSku = $this->createMock(ProductSku::class);
        $productSku->expects(self::once())
            ->method('getValue')
            ->willReturn($skuValue);

        /** @var ProductCategory&MockObject */
        $productCategory = $this->createMock(ProductCategory::class);
        $productCategory->expects(self::once())
            ->method('getValue')
            ->willReturn($categoryValue);

        /** @var ProductPrice&MockObject */
        $productPrice = $this->createMock(ProductPrice::class);
        $productPrice->expects(self::once())
            ->method('getValue')
            ->willReturn($priceValue);

        /** @var Product&MockObject */
        $product = $this->createMock(Product::class);
        $product->expects(self::once())
            ->method('getSku')
            ->willReturn($productSku);
        $product->expects(self::once())
            ->method('getCategory')
            ->willReturn($productCategory);
        $product->expects(self::once())
            ->method('getPrice')
            ->willReturn($productPrice);

        $this->assertEquals(
            [
                'original' => $expectedOriginalPrice,
                'final' => $expectedFinalPrice,
                'discount_percentage' => $discountPercentage,
                'currency' => 'EUR'
            ],
            $this->sut->execute($product)
        );
    }
}
