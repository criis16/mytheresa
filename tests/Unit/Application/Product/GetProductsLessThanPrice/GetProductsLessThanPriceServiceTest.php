<?php

namespace Tests\Unit\Application\Product\GetProductsLessThanPrice;

use InvalidArgumentException;
use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use PHPUnit\Framework\MockObject\MockObject;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;
use App\Application\Product\GetProductsLessThanPrice\GetProductsLessThanPriceService;

class GetProductsLessThanPriceServiceTest extends TestCase
{
    private GetProductsLessThanPriceService $sut;

    /** @var RepositoryInterface&MockObject */
    private RepositoryInterface $repository;

    /** @var ProductAdapter&MockObject */
    private ProductAdapter $productAdapter;

    /** @var GetCurrentPriceService&MockObject */
    private GetCurrentPriceService $getCurrentPriceService;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->productAdapter = $this->createMock(ProductAdapter::class);
        $this->getCurrentPriceService = $this->createMock(GetCurrentPriceService::class);
        $this->sut = new GetProductsLessThanPriceService(
            $this->repository,
            $this->productAdapter,
            $this->getCurrentPriceService
        );
    }

    public function testEmptyCase(): void
    {
        $offset = 0;
        $limit = 5;
        $priceCents = 10055;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No products found with price less than ' . $priceCents);

        $productPrice = new ProductPrice($priceCents);

        $this->repository->expects(self::once())
            ->method('getProductsByPriceLessThan')
            ->with($productPrice)
            ->willReturn([]);

        $this->getCurrentPriceService->expects($this->never())
            ->method('execute');
        $this->productAdapter->expects($this->never())
            ->method('adapt');

        $this->sut->execute($priceCents, $offset, $limit);
    }

    /**
     * @dataProvider getProductsProvider
     */
    public function testGetProductsByCategory(
        array $repositoryProductsResult,
        Product $productInput,
        int $priceCents,
        ProductPrice $productPrice,
        int $offset,
        int $limit,
        array $currentPriceServiceResult,
        array $currentProductAdaptedResult,
        array $expectedResult
    ): void {
        $this->repository->expects(self::once())
            ->method('getProductsByPriceLessThan')
            ->with($productPrice)
            ->willReturn($repositoryProductsResult);

        $this->getCurrentPriceService->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('execute')
            ->with($productInput)
            ->willReturn($currentPriceServiceResult);
        $this->productAdapter->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('adapt')
            ->with($productInput)
            ->willReturn($currentProductAdaptedResult);

        $this->assertEquals($expectedResult, $this->sut->execute($priceCents, $offset, $limit));
    }

    public static function getProductsProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $offset = 0;
        $limit = 5;
        $priceCents = 10055;
        $productPrice = new ProductPrice($priceCents);
        $product = new Product(
            new ProductSku('000001'),
            new ProductName('Fake name'),
            new ProductCategory('boots'),
            $productPrice
        );

        $currentPriceExpectedResult = ['an expected price array'];
        $currentProductAdaptedResult = [
            'an expected adapted product array',
            'price' => 'an expected price'
        ];

        $expectedResult = [
            [
                'an expected adapted product array',
                'price' => $currentPriceExpectedResult
            ]
        ];

        return [
            'repository_products_result' => [$product],
            'product_input' => $product,
            'price_cents' => $priceCents,
            'product_price' => $productPrice,
            'offset' => $offset,
            'limit' => $limit,
            'current_price_service_result' => $currentPriceExpectedResult,
            'current_product_adapted_result' => $currentProductAdaptedResult,
            'expected_output' => $expectedResult
        ];
    }
}
