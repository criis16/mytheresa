<?php

namespace Tests\Unit\Application\Product\Adapters;

use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Entity\Product as EntityProduct;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\Product\Adapters\EntityProductAdapter;
use App\Domain\Product\ProductId;

class EntityProductAdapterTest extends TestCase
{
    private EntityProductAdapter $sut;

    protected function setUp(): void
    {
        $this->sut = new EntityProductAdapter();
    }

    /**
     * @dataProvider entityProductAdapterProvider
     */
    public function testEntityProductAdapter(
        int $entityProductId,
        string $entityProductSku,
        string $entityProductName,
        string $entityProductCategory,
        int $entityProductPrice,
        Product $expectedResult
    ): void {
        /** @var EntityProduct&MockObject */
        $entityProduct = $this->createMock(EntityProduct::class);
        $entityProduct->expects(self::once())
            ->method('getId')
            ->willReturn($entityProductId);
        $entityProduct->expects(self::once())
            ->method('getSku')
            ->willReturn($entityProductSku);
        $entityProduct->expects(self::once())
            ->method('getName')
            ->willReturn($entityProductName);
        $entityProduct->expects(self::once())
            ->method('getCategory')
            ->willReturn($entityProductCategory);
        $entityProduct->expects(self::once())
            ->method('getPrice')
            ->willReturn($entityProductPrice);

        $this->assertEquals($expectedResult, $this->sut->adapt($entityProduct));
    }

    public static function entityProductAdapterProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $entityProductIdValue = 23;
        $entityProductSkuValue = 'sku123';
        $entityProductNameValue = 'a product name';
        $entityProductCategoryValue = 'a product category';
        $entityProductPriceValue = 10000;

        $product = new Product(
            new ProductSku($entityProductSkuValue),
            new ProductName($entityProductNameValue),
            new ProductCategory($entityProductCategoryValue),
            new ProductPrice($entityProductPriceValue)
        );

        $product->setId(new ProductId($entityProductIdValue));

        return [
            'entity_product_id_value' => $entityProductIdValue,
            'entity_product_sku_value' => $entityProductSkuValue,
            'entity_product_name_value' => $entityProductNameValue,
            'entity_product_category_value' => $entityProductCategoryValue,
            'entity_product_price_value' => $entityProductPriceValue,
            'expected_output' => $product
        ];
    }
}
