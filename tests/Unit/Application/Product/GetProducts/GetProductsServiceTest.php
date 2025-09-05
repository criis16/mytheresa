<?php

namespace Tests\Unit\Application\Product\GetProducts;

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
use App\Application\Product\GetProducts\GetProductsService;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsServiceTest extends TestCase
{
    private GetProductsService $sut;

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
        $this->sut = new GetProductsService(
            $this->repository,
            $this->productAdapter,
            $this->getCurrentPriceService
        );
    }

    public function testEmptyCase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No products found');

        $this->repository->expects(self::once())
            ->method('getProducts')
            ->willReturn([]);

        $this->getCurrentPriceService->expects($this->never())
            ->method('execute');
        $this->productAdapter->expects($this->never())
            ->method('adapt');

        $this->assertEquals([], $this->sut->execute());
    }

    /**
     * @dataProvider getProductsProvider
     */
    public function testGetProducts(
        array $repositoryProductsResult,
        Product $productInput,
        array $currentPriceServiceResult,
        array $currentProductAdaptedResult,
        array $expectedResult
    ): void {
        $this->repository->expects(self::once())
            ->method('getProducts')
            ->willReturn($repositoryProductsResult);

        $this->getCurrentPriceService->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('execute')
            ->with($productInput)
            ->willReturn($currentPriceServiceResult);
        $this->productAdapter->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('adapt')
            ->with($productInput)
            ->willReturn($currentProductAdaptedResult);

        $this->assertEquals($expectedResult, $this->sut->execute());
    }

    public static function getProductsProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $product = new Product(
            new ProductSku('000001'),
            new ProductName('Fake name'),
            new ProductCategory('boots'),
            new ProductPrice(10000)
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
            'current_price_service_result' => $currentPriceExpectedResult,
            'current_product_adapted_result' => $currentProductAdaptedResult,
            'expected_output' => $expectedResult
        ];
    }
}
