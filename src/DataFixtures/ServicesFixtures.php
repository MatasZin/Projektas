<?php
namespace App\DataFixtures;

use App\Entity\Services;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServicesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
// create 20 products! Bam!
        for ($i = 0; $i < 40; $i++) {
            $service = new Services();
            $service->setTitle('service' . $i);
            $service->setPrice(mt_rand(10, 100));
            $service->setDescription('You can add descriotion of service here');
            $manager->persist($service);
        }

        $manager->flush();
    }
}