<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\ProductCategory;
use App\Domain\Product\ProductPrice;

interface RepositoryInterface
{
    /**
     * Returns all products
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getProducts(int $offset, int $limit): array;

    /**
     * Returns products by category
     *
     * @param ProductCategory $category
     * @return array
     */
    public function getProductsByCategory(
        ProductCategory $category,
        int $offset,
        int $limit
    ): array;

    /**
     * Returns products by price less than
     *
     * @param ProductPrice $price
     * @return array
     */
    public function getProductsByPriceLessThan(
        ProductPrice $price,
        int $offset,
        int $limit
    ): array;

    /**
     * Returns products by category and price less than
     *
     * @param ProductCategory $category
     * @param ProductPrice $price
     * @return array
     */
    public function getProductsByCategoryAndPriceLessThan(
        ProductCategory $category,
        ProductPrice $price,
        int $offset,
        int $limit
    ): array;

    /**
     * Returns the number of products matching the criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countProducts(array $criteria): int;
}
