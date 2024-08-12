<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    const RESULTS_PER_PAGE = 10;

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

    public function getUserPaginator(User $user, int $currentPage = 1): PaginationInterface
    {
        $query = $this->createQueryBuilder('book')
            ->andWhere('book.owner = :owner')
            ->setParameter('owner', $user)
            ->getQuery();

        return $this->paginatorInterface->paginate($query, $currentPage, self::RESULTS_PER_PAGE);
    }

    public function getSearchPaginator(string $search, int $currentPage = 1): PaginationInterface
    {
        $query = $this->createQueryBuilder('book')
            ->andWhere('book.title LIKE :val')
            ->orWhere('book.author LIKE :val')
            ->setParameter('val', '%' . $search . '%')
            ->getQuery();
        return $this->paginatorInterface->paginate($query, $currentPage, self::RESULTS_PER_PAGE);
    }

    public function getUserSearchPaginator(string $search, User $user, int $currentPage = 1): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('book');

        $queryBuilder->select('book')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like('book.title', ':search'),
                    $queryBuilder->expr()->like('book.author', ':search')
                )
            )
            ->andWhere('book.owner = :owner')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('owner', $user)
            ->getQuery();

        return $this->paginatorInterface->paginate($queryBuilder->getQuery(), $currentPage, self::RESULTS_PER_PAGE);
    }
}
