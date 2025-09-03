<?php

namespace App\Domain\Product;

use App\Domain\Product\ProductId;
use App\Domain\Product\ProductSku;
use App\Domain\Product\ProductName;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;

class Product
{
    private ProductId $id;
    private ProductSku $sku;
    private ProductName $name;
    private ProductCategory $category;
    private ProductPrice $price;

    public function __construct(
        ProductSku $sku,
        ProductName $name,
        ProductCategory $category,
        ProductPrice $price
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
    }

    /**
     * Sets the product id
     *
     * @param ProductId $id
     * @return void
     */
    public function setId(ProductId $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of id
     *
     * @return ProductId
     */
    public function getId(): ProductId
    {
        return $this->id;
    }

    /**
     * Get the value of sku
     *
     * @return ProductSku
     */
    public function getSku(): ProductSku
    {
        return $this->sku;
    }

    /**
     * Get the value of name
     *
     * @return ProductName
     */
    public function getName(): ProductName
    {
        return $this->name;
    }

    /**
     * Get the value of category
     *
     * @return ProductCategory
     */
    public function getCategory(): ProductCategory
    {
        return $this->category;
    }

    /**
     * Get the value of price
     *
     * @return ProductPrice
     */
    public function getPrice(): ProductPrice
    {
        return $this->price;
    }
}
