<?php

namespace App\Infrastructure\Product\Repositories;

use Doctrine\ORM\EntityRepository;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Entity\Product as EntityProduct;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Shared\ConvertPriceToCentsService;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\Adapters\EntityProductAdapter;

class DoctrineRepository implements RepositoryInterface
{
    private const CATEGORY_FIELD = 'category';
    private const PRICE_FIELD = 'price';

    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;
    private EntityProductAdapter $adapter;
    private ConvertPriceToCentsService $convertPriceToCentsService;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityProductAdapter $adapter,
        ConvertPriceToCentsService $convertPriceToCentsService
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(EntityProduct::class);
        $this->adapter = $adapter;
        $this->convertPriceToCentsService = $convertPriceToCentsService;
    }

    public function getProducts(int $offset, int $limit): array
    {
        $queryBuilder = $this->repository->createQueryBuilder('product')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        return $this->applyEntityAdapter($query->getResult());
    }

    public function getProductsByCategory(
        ProductCategory $category,
        int $offset,
        int $limit
    ): array {
        $queryBuilder = $this->repository->createQueryBuilder('product')
            ->where('product.' . self::CATEGORY_FIELD . ' = :category')
            ->setParameter('category', $category->getValue())
            ->orderBy('product.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $this->applyEntityAdapter($queryBuilder->getQuery()->getResult());
    }

    public function getProductsByPriceLessThan(
        ProductPrice $price,
        int $offset,
        int $limit
    ): array {
        $priceInCents = $this->convertPriceToCentsService->execute($price->getValue());

        $queryBuilder = $this->repository->createQueryBuilder('product');
        $queryBuilder->where('product.' . self::PRICE_FIELD . ' < :maxPrice')
            ->setParameter('maxPrice', $priceInCents)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        return $this->applyEntityAdapter($query->getResult());
    }

    public function getProductsByCategoryAndPriceLessThan(
        ProductCategory $category,
        ProductPrice $price,
        int $offset,
        int $limit
    ): array {
        $categoryValue = $category->getValue();
        $priceInCents = $this->convertPriceToCentsService->execute($price->getValue());

        $queryBuilder = $this->repository->createQueryBuilder('product');
        $queryBuilder->where('product.' . self::CATEGORY_FIELD . ' = :category')
            ->andWhere('product.' . self::PRICE_FIELD . ' < :maxPrice')
            ->setParameter('category', $categoryValue)
            ->setParameter('maxPrice', $priceInCents)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();

        return $this->applyEntityAdapter($query->getResult());
    }

    public function countProducts(array $criteria): int
    {
        $qb = $this->repository->createQueryBuilder('product')
            ->select('COUNT(product.id)');

        if (isset($criteria['category'])) {
            $qb->andWhere('product.' . self::CATEGORY_FIELD . ' = :category')
                ->setParameter('category', $criteria['category']);
        }

        if (isset($criteria['priceLessThan'])) {
            $qb->andWhere('product.' . self::PRICE_FIELD . ' < :maxPrice')
                ->setParameter('maxPrice', $criteria['priceLessThan']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Apply the entity adapter to transform EntityProduct objects to domain Product objects
     *
     * @param array $items
     * @return array
     */
    private function applyEntityAdapter(array $items): array
    {
        return \array_map(
            function (EntityProduct $entityProduct) {
                return $this->adapter->adapt($entityProduct);
            },
            $items
        );
    }
}
