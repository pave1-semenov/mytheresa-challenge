<?php

namespace Mytheresa\Challenge\Persistence\Repository;

use Countable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Mytheresa\Challenge\Persistence\Entity\Product;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Here we return iterable instead of array to reduce memory footprint in case
     * of a large amount of data to import
     *
     * @return iterable<string, Product>
     */
    public function getMappedBySku(array $skuList): iterable
    {
        $qb = $this->createQueryBuilder('p', 'p.sku');
        $inExpr = $qb->expr()->in('p.sku', $skuList);

        return $qb->where($inExpr)->getQuery()->toIterable();
    }

    /**
     * Paginator is used to easily calculate the total count of entities affected by query
     * @return Countable<Product>
     */
    public function getFiltered(int $limit, int $offset = 0, ?string $category = null, ?int $priceLessThan = null): Countable
    {
        $qb = $this->createQueryBuilder('p')->setMaxResults($limit)
            ->setFirstResult($offset);
        if ($category) {
            $qb->join('p.category', 'c')->andWhere('c.name = :name')->setParameter('name', $category);
        }
        if ($priceLessThan) {
            $qb->andWhere('p.price <= :price')->setParameter('price', $priceLessThan);
        }

        return new Paginator($qb->getQuery());
    }
}