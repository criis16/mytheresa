<?php

namespace Tests\Unit\Application\Product\Adapters;

use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\Product\Adapters\ProductAdapter;

class ProductAdapterTest extends TestCase
{
    private ProductAdapter $sut;

    protected function setUp(): void
    {
        $this->sut = new ProductAdapter();
    }

    /**
     * @dataProvider productAdapterProvider
     */
    public function testProductAdapter(
        string $productSkuValue,
        string $productNameValue,
        string $productCategoryValue,
        float $productPriceValue,
        array $expectedResult
    ): void {
        /** @var ProductSku&MockObject */
        $productSku = $this->createMock(ProductSku::class);
        $productSku->expects(self::once())
            ->method('getValue')
            ->willReturn($productSkuValue);
        /** @var ProductName&MockObject */
        $productName = $this->createMock(ProductName::class);
        $productName->expects(self::once())
            ->method('getValue')
            ->willReturn($productNameValue);
        /** @var ProductCategory&MockObject */
        $productCategory = $this->createMock(ProductCategory::class);
        $productCategory->expects(self::once())
            ->method('getValue')
            ->willReturn($productCategoryValue);
        /** @var ProductPrice&MockObject */
        $productPrice = $this->createMock(ProductPrice::class);
        $productPrice->expects(self::once())
            ->method('getValue')
            ->willReturn($productPriceValue);

        /** @var Product&MockObject */
        $product = $this->createMock(Product::class);
        $product->expects(self::once())
            ->method('getSku')
            ->willReturn($productSku);
        $product->expects(self::once())
            ->method('getName')
            ->willReturn($productName);
        $product->expects(self::once())
            ->method('getCategory')
            ->willReturn($productCategory);
        $product->expects(self::once())
            ->method('getPrice')
            ->willReturn($productPrice);

        $this->assertEquals($expectedResult, $this->sut->adapt($product));
    }

    public static function productAdapterProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $productSkuValue = 'sku123';
        $productNameValue = 'a product name';
        $productCategoryValue = 'a product category';
        $productPriceValue = 100.00;

        $expectedResult = [
            'sku' => $productSkuValue,
            'name' => $productNameValue,
            'category' => $productCategoryValue,
            'price' => $productPriceValue
        ];

        return [
            'product_sku_value' => $productSkuValue,
            'product_name_value' => $productNameValue,
            'product_category_value' => $productCategoryValue,
            'product_price_value' => $productPriceValue,
            'expected_output' => $expectedResult
        ];
    }
}
