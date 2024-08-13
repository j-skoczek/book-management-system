<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    #[Route('/book/details/{isbn}', name: 'app_book')]
    public function index(string $isbn, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);
        return $this->render('book/index.html.twig', ['book' => $book]);
    }

    //TODO PUT ALL CRUD FUNCTIONS HERE

    //todo php stan and lint

    //TODO COVER IMAGE UPLOAD
    //TODO EVENT LISTENER TO LOG UPLOADS TO A LOG FILE
    //TODO FIXTURE WITH 1MIL RECORDS
}
