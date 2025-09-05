<?php

namespace App\Infrastructure\Product;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Product\GetProducts\GetProductsService;
use App\Application\Product\GetProductsByCategory\GetProductsByCategoryService;
use App\Application\Product\GetProductsLessThanPrice\GetProductsLessThanPriceService;
use App\Application\Product\GetProductsByCategoryLessThanPrice\GetProductsByCategoryLessThanPriceService;

class GetProductsController
{
    private GetProductsService $getProductsService;
    private GetProductsByCategoryService $getProductsByCategoryService;
    private GetProductsLessThanPriceService $getProductsLessThanPriceService;
    private GetProductsByCategoryLessThanPriceService $getProductsByCategoryLessThanPriceService;

    public function __construct(
        GetProductsService $getProductsService,
        GetProductsByCategoryService $getProductsByCategoryService,
        GetProductsLessThanPriceService $getProductsLessThanPriceService,
        GetProductsByCategoryLessThanPriceService $getProductsByCategoryLessThanPriceService
    ) {
        $this->getProductsService = $getProductsService;
        $this->getProductsByCategoryService = $getProductsByCategoryService;
        $this->getProductsLessThanPriceService = $getProductsLessThanPriceService;
        $this->getProductsByCategoryLessThanPriceService = $getProductsByCategoryLessThanPriceService;
    }

    /**
     * Handle the incoming request to get products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProducts(Request $request): JsonResponse
    {
        $products = [];
        $statusCode = 200;
        $responseMessage = 'Products retrieved successfully';
        $queryParams = $request->query->all();

        try {

            if (empty($queryParams)) {
                $products = $this->getProductsService->execute();
            } else {
                $categoryParamExists = \array_key_exists('category', $queryParams);
                $priceLessThanParamExists = \array_key_exists('priceLessThan', $queryParams);
                $category = $categoryParamExists ? $queryParams['category'] : null;
                $priceLessThan = $priceLessThanParamExists ? $queryParams['priceLessThan'] : null;

                if ($categoryParamExists && $priceLessThanParamExists) {
                    $products = $this->getProductsByCategoryLessThanPriceService->execute($category, $priceLessThan);
                } elseif ($categoryParamExists) {
                    $products = $this->getProductsByCategoryService->execute($category);
                } elseif ($priceLessThanParamExists) {
                    $products = $this->getProductsLessThanPriceService->execute($priceLessThan);
                } else {
                    throw new InvalidArgumentException('Invalid query parameters', 400);
                }
            }
        } catch (Exception $e) {
            $responseMessage = $e->getMessage();
            $statusCode = $e->getCode() ?: 400;
        }

        return new JsonResponse(
            [
                'message' => $responseMessage,
                'result' => $products,
            ],
            $statusCode
        );
    }
}
