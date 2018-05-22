<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $encoder;

    public const UsersReffs = array(0=>'user0',1=>'user1',2=>'user2',3=>'user3',4=>'user4',5=>'user5',6=>'user6',
        7=>'user7',8=>'user8',9=>'user9',10=>'user10',11=>'user11',12=>'user12',13=>'user13',
        14=>'user14',15=>'user15',16=>'user16',17=>'user17',18=>'user18',19=>'user19',20=>'paprastas');
    public const WorkersReffs = array(0=>'worker0',1=>'worker1',2=>'worker2',3=>'worker3',4=>'worker4',5=>'worker5',6=>'worker6');

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
// create 20 products! Bam!
        $userNames = Array(
            0  => 'Martynas',
            1  => 'Lukas',
            2  => 'Saulius',
            3  => 'Karolis',
            4  => 'Jonas',
            5  => 'Petras',
            6  => 'Emilis',
            7  => 'Oskaras');
        $userSurnames = Array(
            0  => 'Kavaliauskas',
            1  => 'Petraitis',
            2  => 'Jonaitis',
            3  => 'Jasikevičius',
            4  => 'Pauliukas',
            5  => 'Lukošiūnas',
            6  => 'Janušaitis',
            7  => 'Pocius');
        $userRoles = Array(
            0  => 'ROLE_USER',
            1  => 'ROLE_WORKER',
            2  => 'ROLE_ADMIN');

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setRole(''.$userRoles[0]);
            $name = $userNames[mt_rand(0, 7)];
            $user->setName(''.$name);
            $surname = $userSurnames[mt_rand(0, 7)];
            $user->setSecondName(''.$surname);
            $user->setEmail('' . $name.$surname.$i.'@gmail.com');
            $password = $this->encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setIsDeleted(false);
            $user->setIsActive(true);
            $manager->persist($user);
            $this->addReference(self::UsersReffs[$i], $user);
        }
        for ($i = 20; $i < 26; $i++) {
            $user = new User();
            $user->setRole(''.$userRoles[1]);
            $name = $userNames[mt_rand(0, 7)];
            $user->setName(''.$name);
            $surname = $userSurnames[mt_rand(0, 7)];
            $user->setSecondName(''.$surname);
            $user->setEmail('' . $name.$surname.$i.'@gmail.com');
            $password = $this->encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setIsDeleted(false);
            $user->setIsActive(true);
            $manager->persist($user);
            $this->addReference(self::WorkersReffs[25-$i], $user);
        }
        $user = new User();
        $user->setRole(''.$userRoles[2]);
        $user->setName('admin');
        $user->setSecondName('admin');
        $user->setEmail('admin@admin.com');
        $password = $this->encoder->encodePassword($user, 'admin');
        $user->setPassword($password);
        $user->setIsDeleted(false);
        $user->setIsActive(true);
        $manager->persist($user);

        $user = new User();
        $user->setRole(''.$userRoles[0]);
        $user->setName('paprastas');
        $user->setSecondName('paprastas');
        $user->setEmail('paprastas@paprastas.com');
        $password = $this->encoder->encodePassword($user, 'paprastas');
        $user->setPassword($password);
        $user->setIsDeleted(false);
        $user->setIsActive(true);
        $manager->persist($user);
        $this->addReference(self::UsersReffs[20], $user);

        $user = new User();
        $user->setRole(''.$userRoles[1]);
        $user->setName('worker');
        $user->setSecondName('worker');
        $user->setEmail('worker@worker.com');
        $password = $this->encoder->encodePassword($user, 'worker');
        $user->setPassword($password);
        $user->setIsDeleted(false);
        $user->setIsActive(true);
        $manager->persist($user);
        $this->addReference(self::WorkersReffs[6], $user);

        $manager->flush();
    }

}