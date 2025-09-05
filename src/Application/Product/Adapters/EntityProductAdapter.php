<?php

namespace App\Application\Product\Adapters;

use App\Domain\Product\Product;
use App\Domain\Product\ProductId;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Entity\Product as EntityProduct;

class EntityProductAdapter
{
    /**
     * Adapt an EntityProduct to a Product domain model
     *
     * @param EntityProduct $entityProduct
     * @return Product
     */
    public function adapt(
        EntityProduct $entityProduct
    ): Product {
        $product = new Product(
            new ProductSku($entityProduct->getSku()),
            new ProductName($entityProduct->getName()),
            new ProductCategory($entityProduct->getCategory()),
            new ProductPrice($entityProduct->getPrice())
        );

        $product->setId(
            new ProductId($entityProduct->getId())
        );

        return $product;
    }
}
