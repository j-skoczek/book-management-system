<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use App\EventListener\BookEntityListener;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    const BOOK_COUNT = 10; //change to get x10 records in the db

    private $bookCounter = 0;
    
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function load(ObjectManager $manager): void
    {
        $this->entityManager->getConfiguration()->getEntityListenerResolver()->clear(BookEntityListener::class);
        $this->bookCounter = 0;
        for ($i = 0; $i < UserFixtures::USER_COUNT; $i++) {
            $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);
            $this->addBooks($user, $manager);
        }
        $manager->flush();
    }

    private function addBooks(User $user, ObjectManager $manager)
    {
        for ($i = 0; $i < self::BOOK_COUNT; $i++) {
            $book = new Book();
            $book->setTitle('title ' . $i);
            $book->setAuthor('author ' . $i);
            $book->setDescription('test description ' . $i);
            $book->setYearOfPublication($i);
            $book->setIsbn(str_pad($this->bookCounter++, 13, '0', STR_PAD_LEFT));
            $book->setOwner($user);

            $manager->persist($book);
            
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
