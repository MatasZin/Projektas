<?php
namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CarsFixtures extends Fixture implements DependentFixtureInterface
{

    public const CarsReffs = array(0=>'Car0',1=>'Car1',2=>'Car2',3=>'Car3',4=>'Car4',5=>'Car5',6=>'Car6',
        7=>'Car7',8=>'Car8',9=>'Car9',10=>'Car10',11=>'Car11',12=>'Car12',13=>'Car13',
        14=>'Car14',15=>'Car15',16=>'Car16',17=>'Car17',18=>'Car18',19=>'Car19');
    public const paprastoCarsReffs = array(0=>'paprastoCar0',1=>'paprastoCar1',2=>'paprastoCar2',3=>'paprastoCar3',4=>'paprastoCar4');

    public function load(ObjectManager $manager)
    {
// create 20 products! Bam!
        $carLetters = Array(
            0  => 'AAA',
            1  => 'BBB',
            2  => 'CCC',
            3  => 'DDD',
            4  => 'EEE');
        $carNums = Array(
            0  => '123',
            1  => '111',
            2  => '222',
            3  => '321',
            4  => '333');

        for ($i = 0; $i < 20; $i++) {
            $car = new Car();
            if($i<5)$car->setLicensePlate('' . $carLetters[0].$carNums[$i]);
            if(4<$i && $i<10)$car->setLicensePlate('' . $carLetters[1].$carNums[(9-$i)]);
            if(9<$i && $i<15)$car->setLicensePlate('' . $carLetters[2].$carNums[(14-$i)]);
            if(14<$i && $i<20)$car->setLicensePlate('' . $carLetters[3].$carNums[(19-$i)]);
            $car->setOwner($this->getReference(UsersFixtures::UsersReffs[$i]));
            $manager->persist($car);
            $this->addReference(self::CarsReffs[$i], $car);
        }

        for ($i = 0; $i < 5; $i ++)
        {
            $car = new Car();
            $car->setLicensePlate('' . $carLetters[4].$carNums[$i]);
            $car->setOwner($this->getReference(UsersFixtures::UsersReffs[20]));
            $manager->persist($car);
            $this->addReference(self::paprastoCarsReffs[$i], $car);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UsersFixtures::class,
        );
    }
}