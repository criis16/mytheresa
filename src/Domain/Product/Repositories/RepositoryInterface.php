<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\ProductCategory;
use App\Domain\Product\ProductPrice;

interface RepositoryInterface
{
    /**
     * Returns all products
     *
     * @return array
     */
    public function getProducts(): array;

    /**
     * Returns products by category
     *
     * @param ProductCategory $category
     * @return array
     */
    public function getProductsByCategory(ProductCategory $category): array;

    /**
     * Returns products by price less than
     *
     * @param ProductPrice $price
     * @return array
     */
    public function getProductsByPriceLessThan(ProductPrice $price): array;

    /**
     * Returns products by category and price less than
     *
     * @param ProductCategory $category
     * @param ProductPrice $price
     * @return array
     */
    public function getProductsByCategoryAndPriceLessThan(ProductCategory $category, ProductPrice $price): array;
}
