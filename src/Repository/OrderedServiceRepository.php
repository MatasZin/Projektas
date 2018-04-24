<?php

namespace App\Repository;

use App\Entity\OrderedService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderedService|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedService|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedService[]    findAll()
 * @method OrderedService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedServiceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderedService::class);
    }

    public function getServicesCount($orderId)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(o.id) FROM App\Entity\OrderedService o')->getSingleScalarResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('o')
            ->where('o.something = :value')->setParameter('value', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
