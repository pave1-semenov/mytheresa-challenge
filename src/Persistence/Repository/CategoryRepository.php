<?php

namespace Mytheresa\Challenge\Persistence\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mytheresa\Challenge\Persistence\Entity\Category;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param string[] $names
     * @return Category[]
     */
    public function getMappedByName(array $names): array
    {
        $qb = $this->createQueryBuilder('c', 'c.name');
        $inExpr = $qb->expr()->in('c.name', $names);

        return $qb->where($inExpr)->getQuery()->getResult();
    }
}