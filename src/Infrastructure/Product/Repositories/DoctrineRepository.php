<?php

namespace App\Infrastructure\Product\Repositories;

use Doctrine\ORM\EntityRepository;
use App\Domain\Product\ProductPrice;
use App\Domain\Product\ProductCategory;
use App\Entity\Product as EntityProduct;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Product\Repositories\RepositoryInterface;
use App\Application\Product\Adapters\EntityProductAdapter;

class DoctrineRepository implements RepositoryInterface
{
    private const CATEGORY_FIELD = 'category';
    private const PRICE_FIELD = 'price';

    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;
    private EntityProductAdapter $adapter;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityProductAdapter $adapter
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(EntityProduct::class);
        $this->adapter = $adapter;
    }

    public function getProducts(): array
    {
        return $this->applyEntityAdapter($this->repository->findAll());
    }

    public function getProductsByCategory(ProductCategory $category): array
    {
        return $this->applyEntityAdapter(
            $this->repository->findBy(
                [self::CATEGORY_FIELD => $category->getValue()]
            )
        );
    }

    public function getProductsByPriceLessThan(ProductPrice $price): array
    {
        $priceInCents = $this->convertPriceToCents($price->getValue());

        $queryBuilder = $this->repository->createQueryBuilder('product');
        $queryBuilder->where('product.' . self::PRICE_FIELD . ' < :maxPrice')
            ->setParameter('maxPrice', $priceInCents);

        $query = $queryBuilder->getQuery();

        return $this->applyEntityAdapter($query->getResult());
    }

    public function getProductsByCategoryAndPriceLessThan(ProductCategory $category, ProductPrice $price): array
    {
        $categoryValue = $category->getValue();
        $priceInCents = $this->convertPriceToCents($price->getValue());

        $queryBuilder = $this->repository->createQueryBuilder('product');
        $queryBuilder->where('product.' . self::CATEGORY_FIELD . ' = :category')
            ->andWhere('product.' . self::PRICE_FIELD . ' < :maxPrice')
            ->setParameter('category', $categoryValue)
            ->setParameter('maxPrice', $priceInCents);

        $query = $queryBuilder->getQuery();

        return $this->applyEntityAdapter($query->getResult());
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
