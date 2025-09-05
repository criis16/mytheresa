<?php

namespace App\Application\Product\GetProductsByCategory;

use InvalidArgumentException;
use App\Domain\Product\ProductCategory;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsByCategoryService
{
    private RepositoryInterface $repository;
    private ProductAdapter $adapter;
    private GetCurrentPriceService $getCurrentPriceService;

    public function __construct(
        RepositoryInterface $repository,
        ProductAdapter $adapter,
        GetCurrentPriceService $getCurrentPriceService
    ) {
        $this->repository = $repository;
        $this->adapter = $adapter;
        $this->getCurrentPriceService = $getCurrentPriceService;
    }

    /**
     * Get all products in a specific category with their current prices
     *
     * @param string $category
     * @return array
     * @throws InvalidArgumentException if no products found for the category
     */
    public function execute(string $category): array
    {
        $adaptedProducts = [];
        $products = $this->repository->getProductsByCategory(
            new ProductCategory($category)
        );

        if (empty($products)) {
            throw new InvalidArgumentException('No products found for the specified category', 404);
        }

        foreach ($products as $product) {
            $currentPrice = $this->getCurrentPriceService->execute($product);
            $currentProduct = $this->adapter->adapt($product);
            $currentProduct['price'] = $currentPrice;
            $adaptedProducts[] = $currentProduct;
        }

        return $adaptedProducts;
    }
}
