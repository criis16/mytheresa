<?php

namespace App\Application\Product\GetCurrentPrice;

use App\Domain\Product\Product;

class GetCurrentPriceService
{
    private const CURRENCY = 'EUR';
    private const DISCOUNT_BY_CATEGORY = [
        'boots' => 30
    ];
    private const DISCOUNT_BY_PRODUCTS = [
        '000003' => 15
    ];

    /**
     * Calculate the current price of a product considering applicable discounts
     *
     * @param Product $product
     * @return array
     */
    public function execute(Product $product): array
    {
        $productDiscountMessage = null;
        $productDiscountPercentage = $this->getProductDiscountPercentage($product);
        $originalPrice = $finalPrice = $product->getPrice()->getValue();

        if ($productDiscountPercentage > 0) {
            $finalPrice = $this->applyDiscountPercentage($originalPrice, $productDiscountPercentage);
            $productDiscountMessage = $productDiscountPercentage . '%';
        }

        return [
            'original' => $this->convertPriceToCents($originalPrice),
            'final' => $this->convertPriceToCents($finalPrice),
            'discount_percentage' => $productDiscountMessage,
            'currency' => self::CURRENCY
        ];
    }

    /**
     * Get the highest discount percentage applicable to the product
     *
     * @param Product $product
     * @return integer
     */
    private function getProductDiscountPercentage(Product $product): int
    {
        $discounts = [0];
        $productSku = $product->getSku()->getValue();
        $productCategory = \strtolower($product->getCategory()->getValue());

        if (\array_key_exists($productCategory, self::DISCOUNT_BY_CATEGORY)) {
            $discounts[] = self::DISCOUNT_BY_CATEGORY[$productCategory];
        }

        if (\array_key_exists($productSku, self::DISCOUNT_BY_PRODUCTS)) {
            $discounts[] = self::DISCOUNT_BY_PRODUCTS[$productSku];
        }

        return \max($discounts);
    }

    /**
     * Apply discount percentage to the price
     *
     * @param float $price
     * @param integer $discountPercentage
     * @return float
     */
    private function applyDiscountPercentage(float $price, int $discountPercentage): float
    {
        return $price - ($price * $discountPercentage / 100);
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
