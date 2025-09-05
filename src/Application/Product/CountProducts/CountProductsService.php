<?php

namespace App\Application\Product\CountProducts;

use App\Domain\Product\Repositories\RepositoryInterface;

class CountProductsService
{
    private RepositoryInterface $repository;

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Count the number of products matching the criteria
     *
     * @param array $criteria
     * @return integer
     */
    public function execute(array $criteria): int
    {
        return $this->repository->countProducts($criteria);
    }
}
