<?php
namespace App\DataFixtures;

use App\Entity\Services;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServicesFixtures extends Fixture
{

    public const ServicesReffs = array(0 => 'service01', 1=> 'service02', 2=> 'service03', 3 => 'service04',
        4 => 'service05',5 => 'service06', 6=> 'service07', 7=> 'service08', 8 => 'service09',
        9 => 'service10',10=> 'service11', 11=> 'service12',12 => 'service13',13=> 'service14',
        14=> 'service15', 15=> 'service16',16 => 'service17',17=> 'service18', 18=> 'service19',
        19 => 'service20',20=> 'service21', 21=> 'service22',22 => 'service23',23=> 'service24',
        24=> 'service25',25 => 'service26',26=> 'service27', 27=> 'service28', 28=> 'service29',
        29 => 'service30',30=> 'service31', 31=> 'service32',32=> 'service33',33 => 'service34',
        34=> 'service35', 35=> 'service36',36 => 'service37',37=> 'service38', 38=> 'service39', 39=> 'service40');
    public function load(ObjectManager $manager)
    {
// create 20 products! Bam!
        for ($i = 0; $i < 40; $i++) {
            $service = new Services();
            $service->setTitle('service' . $i);
            $service->setPrice(mt_rand(10, 100));
            $service->setDescription('You can add descriotion of service here');
            $service->setIsActive(true);
            $manager->persist($service);
            $this->addReference(self::ServicesReffs[$i], $service);
        }
        $manager->flush();
    }
}