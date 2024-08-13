<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[WithMonologChannel('book')]
#[AsEntityListener(event: Events::prePersist, entity: Book::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Book::class)]
final class BookEntityListener
{
    const UPDATE_MSG = 'Book with ISBN :isbn has been edited by user :user';
    const SAVE_MSG = 'Book with ISBN :isbn has been added by user :user';
    
    public function __construct(
        private LoggerInterface $bookLogger,
        private Security $security
    ) {}

    public function prePersist(Book $book, LifecycleEventArgs $event)
    {
        $msg = str_replace(':isbn', $book->getIsbn(), self::SAVE_MSG);
        $msg = str_replace(':user', $this->security->getUser()->getUserIdentifier(), $msg);
        $this->bookLogger->info($msg);
    }

    public function preUpdate(Book $book, LifecycleEventArgs $event)
    {
        $msg = str_replace(':isbn', $book->getIsbn(), self::UPDATE_MSG);
        $msg = str_replace(':user', $this->security->getUser()->getUserIdentifier(), $msg);
        $this->bookLogger->info($msg);
    }
}
