<?php

namespace App\Application\Product\GetProductsLessThanPrice;

use InvalidArgumentException;
use App\Domain\Product\ProductPrice;
use App\Application\Product\Adapters\ProductAdapter;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\GetCurrentPrice\GetCurrentPriceService;

class GetProductsLessThanPriceService
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
     * Get all products with a price less than the specified price
     *
     * @param float $priceLessThan
     * @return array
     * @throws InvalidArgumentException
     */
    public function execute(float $priceLessThan): array
    {
        $adaptedProducts = [];
        $products = $this->repository->getProductsByPriceLessThan(
            new ProductPrice($this->convertPriceToCents($priceLessThan))
        );

        if (empty($products)) {
            throw new InvalidArgumentException('No products found with price less than ' . $priceLessThan);
        }

        foreach ($products as $product) {
            $currentPrice = $this->getCurrentPriceService->execute($product);
            $currentProduct = $this->adapter->adapt($product, $currentPrice);
            $currentProduct['price'] = $currentPrice;
            $adaptedProducts[] = $currentProduct;
        }

        return $adaptedProducts;
    }

    /**
     * Convert price to cents
     *
     * @param float $price
     * @return integer
     */
    private function convertPriceToCents(float $price): int
    {
        return (int) \round($price * 100);
    }
}
