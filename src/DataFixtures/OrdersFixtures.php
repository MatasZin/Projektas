<?php
namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class OrdersFixtures extends Fixture implements DependentFixtureInterface
{
    public const OrdersReffs = array(0 => '01', 1=> '02', 2=> '03', 3 => '04',4 => '05',5 => '06', 6=> '07', 7=> '08', 8 => '09',
        9 => '10',10=> '11', 11=> '12',12 => '13',13=> '14',14=> '15', 15=> '16',16 => '17',17=> '18', 18=> '19',
        19 => '20',20=> '21', 21=> '22',22 => '23',23=> '24', 24=> '25',25 => '26',26=> '27', 27=> '28', 28=> '29',
        29 => '30',30=> '31', 31=> '32',32=> '33',33 => '34',34=> '35', 35=> '36',36 => '37',37=> '38', 38=> '39', 39=> '40');


    public function load(ObjectManager $manager)
    {
        $year = array(0 => '2015', 1=> '2016', 2=> '2017', 3 => '2018');
        $month = array(0 => '01', 1=> '02', 2=> '03', 3 => '04',4 => '05',5 => '06', 6=> '07', 7=> '08', 8 => '09',
            9 => '10',10=> '11', 11=> '12');
        $day = array(0 => '01', 1=> '02', 2=> '03', 3 => '04',4 => '05',5 => '06', 6=> '07', 7=> '08', 8 => '09',
            9 => '10',10=> '11', 11=> '12',12 => '13',13=> '14',14=> '15', 15=> '16',16 => '17',17=> '18', 18=> '19',
            19 => '20',20=> '21', 21=> '22',22 => '23',23=> '24', 24=> '25',25 => '26',26=> '27', 27=> '28');
        $hour = array(0 => '08', 1=> '09', 2=> '10', 3 => '11',4 => '12',5 => '13', 6=> '14', 7=> '15', 8 => '16');

        for ($i = 0; $i < 20; $i++) {
            $order = new Order();
            $order->setCompleted(1);
            $order->setCar(($this->getReference(CarsFixtures::CarsReffs[$i])));
            $order->setOrderDate(date_create(''.$year[mt_rand(0,2)].'-'.$month[mt_rand(0,11)].'-'.$day[mt_rand(0,27)].' '.$hour[mt_rand(0,8)].':00:00'));
            $manager->persist($order);
            $this->addReference(self::OrdersReffs[$i], $order);
        }
        for ($i = 0; $i < 20; $i++) {
            $order = new Order();
            $order->setCompleted(1);
            $order->setCar(($this->getReference(CarsFixtures::CarsReffs[$i])));
            $order->setOrderDate(date_create(''.$year[mt_rand(0,2)].'-'.$month[mt_rand(0,11)].'-'.$day[mt_rand(0,27)].' '.$hour[mt_rand(0,8)].':00:00'));
            $manager->persist($order);
            $this->addReference(self::OrdersReffs[20+$i], $order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CarsFixtures::class,
        );
    }
}