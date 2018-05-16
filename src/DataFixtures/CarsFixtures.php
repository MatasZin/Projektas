<?php
namespace App\DataFixtures;

use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CarsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {/*
// create 20 products! Bam!
        for ($i = 0; $i < 40; $i++) {
            $car = new Car();
            $car->setTitle('service' . $i);
            $car->setPrice(mt_rand(10, 100));
            $car->setDescription('You can add descriotion of service here');
            $manager->persist($car);
        }

        $manager->flush();*/
    }
    public function getDependencies()
    {
        return array(
            UsersFixtures::class,
        );
    }
}