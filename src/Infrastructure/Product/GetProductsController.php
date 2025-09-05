<?php

namespace App\Infrastructure\Product;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Product\GetProducts\GetProductsService;
use App\Application\Product\CountProducts\CountProductsService;
use App\Application\Product\GetProductsByCategory\GetProductsByCategoryService;
use App\Application\Product\GetProductsLessThanPrice\GetProductsLessThanPriceService;
use App\Application\Product\GetProductsByCategoryLessThanPrice\GetProductsByCategoryLessThanPriceService;

class GetProductsController
{
    private const LIMIT = 5;
    private const DEFAULT_PAGE = 1;

    private GetProductsService $getProductsService;
    private GetProductsByCategoryService $getProductsByCategoryService;
    private GetProductsLessThanPriceService $getProductsLessThanPriceService;
    private GetProductsByCategoryLessThanPriceService $getProductsByCategoryLessThanPriceService;
    private CountProductsService $countProductsService;

    public function __construct(
        GetProductsService $getProductsService,
        GetProductsByCategoryService $getProductsByCategoryService,
        GetProductsLessThanPriceService $getProductsLessThanPriceService,
        GetProductsByCategoryLessThanPriceService $getProductsByCategoryLessThanPriceService,
        CountProductsService $countProductsService
    ) {
        $this->getProductsService = $getProductsService;
        $this->getProductsByCategoryService = $getProductsByCategoryService;
        $this->getProductsLessThanPriceService = $getProductsLessThanPriceService;
        $this->getProductsByCategoryLessThanPriceService = $getProductsByCategoryLessThanPriceService;
        $this->countProductsService = $countProductsService;
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
        $categoryParamExists = \array_key_exists('category', $queryParams);
        $priceLessThanParamExists = \array_key_exists('priceLessThan', $queryParams);
        $pageParamExists = \array_key_exists('page', $queryParams);
        $limitParamExists = \array_key_exists('limit', $queryParams);
        $page = $pageParamExists ? (int) $queryParams['page'] : self::DEFAULT_PAGE;
        $page = $page < 1 ? self::DEFAULT_PAGE : $page;
        $limit = $limitParamExists ? (int) $queryParams['limit'] : self::LIMIT;
        $offset = ($page - 1) * $limit;

        try {
            if (!$categoryParamExists && !$priceLessThanParamExists) {
                $products = $this->getProductsService->execute($offset, $limit);
                $totalProducts = $this->countProductsService->execute([]);
            } else {
                $category = $categoryParamExists ? $queryParams['category'] : null;
                $priceLessThan = $priceLessThanParamExists ? $queryParams['priceLessThan'] : null;

                if ($categoryParamExists && $priceLessThanParamExists) {
                    $products = $this->getProductsByCategoryLessThanPriceService->execute(
                        $category,
                        $priceLessThan,
                        $offset,
                        $limit
                    );
                    $totalProducts = $this->countProductsService->execute([
                        'category' => $category,
                        'priceLessThan' => $priceLessThan
                    ]);
                } elseif ($categoryParamExists) {
                    $products = $this->getProductsByCategoryService->execute(
                        $category,
                        $offset,
                        $limit
                    );
                    $totalProducts = $this->countProductsService->execute(['category' => $category]);
                } elseif ($priceLessThanParamExists) {
                    $products = $this->getProductsLessThanPriceService->execute(
                        $priceLessThan,
                        $offset,
                        $limit
                    );
                    $totalProducts = $this->countProductsService->execute(['priceLessThan' => $priceLessThan]);
                } else {
                    throw new InvalidArgumentException('Invalid query parameters', 400);
                }
            }

            $totalPages = (int) \ceil($totalProducts / $limit);
        } catch (Exception $e) {
            $responseMessage = $e->getMessage();
            $statusCode = $e->getCode() ?: 400;
        }

        return new JsonResponse(
            [
                'message' => $responseMessage,
                'result' => $products,
                'pagination' => [
                    'currentPage' => $page,
                    'perPage' => $limit,
                    'totalPages' => $totalPages ?? self::DEFAULT_PAGE,
                    'totalProducts' => $totalProducts ?? 0
                ]
            ],
            $statusCode
        );
    }
}
