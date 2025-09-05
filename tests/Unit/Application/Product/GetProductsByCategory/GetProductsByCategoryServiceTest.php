<?php

namespace Tests\Unit\Application\Product\GetProductsByCategory;

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
use App\Application\Product\GetProductsByCategory\GetProductsByCategoryService;
use InvalidArgumentException;

class GetProductsByCategoryServiceTest extends TestCase
{
    private GetProductsByCategoryService $sut;

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
        $this->sut = new GetProductsByCategoryService(
            $this->repository,
            $this->productAdapter,
            $this->getCurrentPriceService
        );
    }

    public function testEmptyCase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No products found for the specified category');

        $categoryInput = 'a category';
        $productCategory = new ProductCategory($categoryInput);

        $this->repository->expects(self::once())
            ->method('getProductsByCategory')
            ->with($productCategory)
            ->willReturn([]);

        $this->getCurrentPriceService->expects($this->never())
            ->method('execute');
        $this->productAdapter->expects($this->never())
            ->method('adapt');

        $this->sut->execute($categoryInput);
    }

    /**
     * @dataProvider getProductsProvider
     */
    public function testGetProductsByCategory(
        array $repositoryProductsResult,
        Product $productInput,
        string $productCategoryInput,
        ProductCategory $productCategory,
        array $currentPriceServiceResult,
        array $currentProductAdaptedResult,
        array $expectedResult
    ): void {
        $this->repository->expects(self::once())
            ->method('getProductsByCategory')
            ->with($productCategory)
            ->willReturn($repositoryProductsResult);

        $this->getCurrentPriceService->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('execute')
            ->with($productInput)
            ->willReturn($currentPriceServiceResult);
        $this->productAdapter->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('adapt')
            ->with($productInput)
            ->willReturn($currentProductAdaptedResult);

        $this->assertEquals($expectedResult, $this->sut->execute($productCategoryInput));
    }

    public static function getProductsProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $productCategoryInput = 'boots';
        $productCategory = new ProductCategory($productCategoryInput);
        $product = new Product(
            new ProductSku('000001'),
            new ProductName('Fake name'),
            $productCategory,
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
            'product_category_input' => $productCategoryInput,
            'product_category' => $productCategory,
            'current_price_service_result' => $currentPriceExpectedResult,
            'current_product_adapted_result' => $currentProductAdaptedResult,
            'expected_output' => $expectedResult
        ];
    }
}
