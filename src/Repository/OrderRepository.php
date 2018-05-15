<?php

namespace App\Repository;

use App\Criteria\OrderFilter;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByOrderFilter(OrderFilter $filter)
    {
        $querry = $this->createQueryBuilder('o');
        if (!$filter->getCompleteness()) {
            $querry->where('o.completed = 0');
        }
        $querry->add('orderBy', "o." . $filter->getOrderby() . " " . $filter->getSortorder());
        return $querry->getQuery()->getResult();
    }


    public function countHowManyOrdersTheCarHave($car)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT(o.id)
                      FROM App\Entity\Order o
                      INNER JOIN App\Entity\Car c WITH o.car = c.id
                      WHERE c.id = :carsid'
            )
            ->setParameter('carsid', $car)
            ->getSingleScalarResult();
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
