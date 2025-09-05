<?php

namespace App\Application\Product\Adapters;

use App\Domain\Product\Product;

class ProductAdapter
{
    /**
     * Adapt a Product domain model to an array
     *
     * @param Product $product
     * @return array
     */
    public function adapt(
        Product $product
    ): array {
        return [
            'sku' => $product->getSku()->getValue(),
            'name' => $product->getName()->getValue(),
            'category' => $product->getCategory()->getValue(),
            'price' => $product->getPrice()->getValue()
        ];
    }
}
