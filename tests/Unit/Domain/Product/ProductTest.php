<?php

namespace Tests\Unit\Domain\Product;

use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductId;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use PHPUnit\Framework\MockObject\MockObject;

class ProductTest extends TestCase
{
    private Product $sut;

    /** @var ProductId&MockObject */
    private ProductId $id;

    /** @var ProductSku&MockObject */
    private ProductSku $sku;

    /** @var ProductName&MockObject */
    private ProductName $name;

    /** @var ProductCategory&MockObject */
    private ProductCategory $category;

    /** @var ProductPrice&MockObject */
    private ProductPrice $price;

    protected function setUp(): void
    {
        $this->id = $this->createMock(ProductId::class);
        $this->sku = $this->createMock(ProductSku::class);
        $this->name = $this->createMock(ProductName::class);
        $this->category = $this->createMock(ProductCategory::class);
        $this->price = $this->createMock(ProductPrice::class);

        $this->sut = new Product(
            $this->sku,
            $this->name,
            $this->category,
            $this->price
        );
    }

    public function testGetSku(): void
    {
        $this->assertSame($this->sku, $this->sut->getSku());
    }

    public function testGetName(): void
    {
        $this->assertSame($this->name, $this->sut->getName());
    }

    public function testGetCategory(): void
    {
        $this->assertSame($this->category, $this->sut->getCategory());
    }

    public function testGetPrice(): void
    {
        $this->assertSame($this->price, $this->sut->getPrice());
    }

    public function testSetAndGetId(): void
    {
        $this->id = $this->createMock(ProductId::class);

        $this->sut->setId($this->id);

        $this->assertSame($this->id, $this->sut->getId());
    }
}
