<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookDeleteController extends AbstractController
{
    #[Route('/book/delete/{bookId}', name: 'app_book_delete', methods:'POST')]
    public function delete(int $bookId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        
        if (!$book) {
            throw $this->createNotFoundException('No book found');
        }
        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_book_list');
    }
}
