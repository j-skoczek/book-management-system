<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    const RESULTS_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Book::class);
    }

    public function getPaginator(int $currentPage = 1): PaginationInterface
    {
        $query = $this->createQueryBuilder('book')
            ->getQuery();
            
        return $this->paginatorInterface->paginate($query, $currentPage, self::RESULTS_PER_PAGE);
    }

    public function findByTitleOrAuthorPaginator(string $search, int $currentPage = 1): PaginationInterface
    {
        $query = $this->createQueryBuilder('book')
            ->andWhere('book.title LIKE :val')
            ->orWhere('book.author LIKE :val')
            ->setParameter('val', '%' . $search . '%')
            ->getQuery();
            return $this->paginatorInterface->paginate($query, $currentPage, self::RESULTS_PER_PAGE);
    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
