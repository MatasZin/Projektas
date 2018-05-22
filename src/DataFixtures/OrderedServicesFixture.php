<?php
namespace App\DataFixtures;

use App\Entity\OrderedService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderedServicesFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 40; $i++) {
            $serviceStart = mt_rand(0, 34);
            for ($j = 0; $j < mt_rand(1,5);$j++) {
                $service = new OrderedService();
                $service->setLastChangeDate();
                $service->setStatus('Done!');
                $service->setOrder($this->getReference(OrdersFixtures::OrdersReffs[$i]));
                $service->setWorker($this->getReference(UsersFixtures::WorkersReffs[mt_rand(0, 6)]));
                $service->setService($this->getReference(ServicesFixtures::ServicesReffs[$serviceStart + $j]));
                $manager->persist($service);
            }
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return array(
            OrdersFixtures::class,
            CarsFixtures::class,
            UsersFixtures::class,
            ServicesFixtures::class
        );
    }
}