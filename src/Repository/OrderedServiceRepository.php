<?php

namespace App\Repository;

use App\Entity\OrderedService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Criteria\JobFilter;

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

    public function findByJobFilter(JobFilter $filter, $id)
    {
        $querry = $this->createQueryBuilder('j');

        if (!$filter->getCompleteness())
            $querry->where('j.status != :status')->setParameter('status', 'Done!');

        if($id !== null)
            $querry->andWhere('j.worker = :id')->setParameter('id', $id);

        if($filter->getOrderby() === 'licensePlate') {
            $querry->from('App\Entity\Car', 'c')->from('App\Entity\Order', 'o');
            $querry->andWhere('j.order = o.id')->andWhere('o.car = c.id');
            $querry->add('orderBy', "c.licensePlate" . " " . $filter->getSortorder());
        }
        else {
            $querry->add('orderBy', "j." . $filter->getOrderby() . " " . $filter->getSortorder());
        }
        return $querry->getQuery()->getResult();
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
