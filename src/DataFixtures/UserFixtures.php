<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    const USER_REFERENCE = 'user-class-';
    const USER_COUNT = 10;
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher) {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::USER_COUNT; $i++) {
            $user = new User();
            $user->setEmail('test+' . $i . '@test.test');
            $user->setRoles(['roles' => 'ROLE_USER']);
            $hashedPassword = $this->userPasswordHasher->hashPassword(
                $user,
                'asd123'
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }
        
        $manager->flush();
        $manager->clear();
    }
}
