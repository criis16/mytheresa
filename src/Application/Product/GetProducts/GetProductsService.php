<?php

namespace App\Application\Product\GetProducts;

use InvalidArgumentException;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsService
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
     * Get all products with their current prices
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function execute(
        int $offset,
        int $limit
    ): array {
        $adaptedProducts = [];
        $products = $this->repository->getProducts(
            $offset,
            $limit
        );

        if (empty($products)) {
            throw new InvalidArgumentException('No products found');
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
