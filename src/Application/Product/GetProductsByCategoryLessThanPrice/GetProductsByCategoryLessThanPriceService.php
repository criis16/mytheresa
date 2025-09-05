<?php

namespace App\Application\Product\GetProductsByCategoryLessThanPrice;

use InvalidArgumentException;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Domain\Shared\ConvertPriceToCentsService;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsByCategoryLessThanPriceService
{
    private RepositoryInterface $repository;
    private ProductAdapter $adapter;
    private GetCurrentPriceService $getCurrentPriceService;
    private ConvertPriceToCentsService $convertPriceToCentsService;

    public function __construct(
        RepositoryInterface $repository,
        ProductAdapter $adapter,
        GetCurrentPriceService $getCurrentPriceService,
        ConvertPriceToCentsService $convertPriceToCentsService
    ) {
        $this->repository = $repository;
        $this->adapter = $adapter;
        $this->getCurrentPriceService = $getCurrentPriceService;
        $this->convertPriceToCentsService = $convertPriceToCentsService;
    }

    /**
     * Get products by category and price less than the specified amount
     *
     * @param string $category
     * @param float $priceLessThan
     * @return array
     * @throws InvalidArgumentException if no products are found
     */
    public function execute(string $category, float $priceLessThan): array
    {
        $adaptedProducts = [];
        $products = $this->repository->getProductsByCategoryAndPriceLessThan(
            new ProductCategory($category),
            new ProductPrice($this->convertPriceToCentsService->execute($priceLessThan))
        );

        if (empty($products)) {
            throw new InvalidArgumentException(
                'No products found for the specified category and price less than ' . $priceLessThan
            );
        }

        foreach ($products as $product) {
            $currentPrice = $this->getCurrentPriceService->execute($product);
            $currentProduct = $this->adapter->adapt($product, $currentPrice);
            $currentProduct['price'] = $currentPrice;
            $adaptedProducts[] = $currentProduct;
        }

        return $adaptedProducts;
    }
}
