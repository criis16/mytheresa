<?php

namespace App\Application\Product\GetProductsByCategoryLessThanPrice;

use InvalidArgumentException;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsByCategoryLessThanPriceService
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
     * Get products by category and price less than the specified amount
     *
     * @param string $category
     * @param integer $priceLessThan
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws InvalidArgumentException if no products are found
     */
    public function execute(
        string $category,
        int $priceLessThan,
        int $offset,
        int $limit
    ): array {
        $adaptedProducts = [];
        $products = $this->repository->getProductsByCategoryAndPriceLessThan(
            new ProductCategory($category),
            new ProductPrice($priceLessThan),
            $offset,
            $limit
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
