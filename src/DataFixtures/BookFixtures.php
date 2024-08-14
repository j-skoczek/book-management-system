<?php

namespace App\DataFixtures;

use Generator;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use App\EventListener\BookEntityListener;
use Doctrine\Bundle\FixturesBundle\Fixture;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    const BOOK_COUNT = 10000; //change to get x10 records in the db
    const BATCH_SIZE = 10000; //change to get x10 records in the db

    private $bookCounter = 0;
    private $consoleOutput;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->consoleOutput = new ConsoleOutput();
    }

    public function load(ObjectManager $manager): void
    {
        $this->entityManager->getConfiguration()->getEntityListenerResolver()->clear(BookEntityListener::class);
        $this->bookCounter = 0;
        for ($i = 0; $i < UserFixtures::USER_COUNT; $i++) {
            $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);
            $this->addBooks($user, $manager);
        }
        $manager->flush();
        $manager->clear();
    }

    private function addBooks(User $user, ObjectManager $manager)
    {
        foreach ($this->generateBooks($user) as $book) {
            $manager->persist($book);
            if ($this->bookCounter % self::BATCH_SIZE === 0) {
                $this->consoleOutput->writeln('<info>saved ' . $this->bookCounter . ' books</info>');
                $manager->flush();
            }
        }
    }

    private function generateBooks(User $user): Generator
    {
        for ($i = 0; $i < self::BOOK_COUNT; $i++) {
            $book = new Book();
            $book->setTitle('title ' . $i);
            $book->setAuthor('author ' . $i);
            $book->setDescription('test description ' . $i);
            $book->setYearOfPublication($i);
            $book->setIsbn(str_pad($this->bookCounter++, 13, '0', STR_PAD_LEFT));
            $book->setOwner($user);

            yield $book;
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
