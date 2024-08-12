<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookEditController extends AbstractController
{
    #[Route('/book/edit/{id}', name: 'app_book_edit', methods: ["GET", "POST"])]
    public function edit(EntityManagerInterface $entityManager, Request $request, int $id, #[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found');
        }

        //todo check if book belongs to the user

        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($cover = $editForm['coverFileName']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $cover->guessExtension();
                $cover->move($photoDir, $filename);
                $book->setCoverFileName($filename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_book', ['isbn' => $book->getIsbn()]);
        }

        return $this->render('book_edit/index.html.twig', [
            'edit_form' => $editForm,
        ]);
    }
}
