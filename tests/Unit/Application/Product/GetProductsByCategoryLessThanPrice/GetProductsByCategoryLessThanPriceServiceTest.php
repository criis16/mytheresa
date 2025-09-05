<?php

namespace Tests\Unit\Application\Product\GetProductsByCategoryLessThanPrice;

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
use App\Application\Product\GetProductsByCategoryLessThanPrice\GetProductsByCategoryLessThanPriceService;

class GetProductsByCategoryLessThanPriceServiceTest extends TestCase
{
    private GetProductsByCategoryLessThanPriceService $sut;

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
        $this->sut = new GetProductsByCategoryLessThanPriceService(
            $this->repository,
            $this->productAdapter,
            $this->getCurrentPriceService
        );
    }

    public function testEmptyCase(): void
    {
        $priceInput = 100.55;
        $priceCents = 10055;
        $categoryInput = 'a category';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No products found for the specified category and price less than ' . $priceInput);

        $productPrice = new ProductPrice($priceCents);
        $productCategory = new ProductCategory($categoryInput);

        $this->repository->expects(self::once())
            ->method('getProductsByCategoryAndPriceLessThan')
            ->with($productCategory, $productPrice)
            ->willReturn([]);

        $this->getCurrentPriceService->expects($this->never())
            ->method('execute');
        $this->productAdapter->expects($this->never())
            ->method('adapt');

        $this->sut->execute($categoryInput, $priceInput);
    }

    /**
     * @dataProvider getProductsProvider
     */
    public function testGetProductsByCategory(
        array $repositoryProductsResult,
        Product $productInput,
        float $priceInput,
        string $categoryInput,
        ProductPrice $productPrice,
        ProductCategory $productCategory,
        array $currentPriceServiceResult,
        array $currentProductAdaptedResult,
        array $expectedResult
    ): void {
        $this->repository->expects(self::once())
            ->method('getProductsByCategoryAndPriceLessThan')
            ->with($productCategory, $productPrice)
            ->willReturn($repositoryProductsResult);

        $this->getCurrentPriceService->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('execute')
            ->with($productInput)
            ->willReturn($currentPriceServiceResult);
        $this->productAdapter->expects(self::exactly(\count($repositoryProductsResult)))
            ->method('adapt')
            ->with($productInput)
            ->willReturn($currentProductAdaptedResult);

        $this->assertEquals($expectedResult, $this->sut->execute($categoryInput, $priceInput));
    }

    public static function getProductsProvider(): array
    {
        return [
            'simple_case' => self::simpleCase()
        ];
    }

    private static function simpleCase(): array
    {
        $priceInput = 100.55;
        $priceCents = 10055;
        $categoryInput = 'boots';
        $productCategory = new ProductCategory($categoryInput);
        $productPrice = new ProductPrice($priceCents);
        $product = new Product(
            new ProductSku('000001'),
            new ProductName('Fake name'),
            $productCategory,
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
            'product_price_input' => $priceInput,
            'product_category_input' => $categoryInput,
            'product_price' => $productPrice,
            'product_category' => $productCategory,
            'current_price_service_result' => $currentPriceExpectedResult,
            'current_product_adapted_result' => $currentProductAdaptedResult,
            'expected_output' => $expectedResult
        ];
    }
}
