<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookUploadController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    //todo change ctrl name. will handle all crud prolly


    #[Route('/book/upload', name: 'app_book_upload')]
    public function index(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('book_upload/index.html.twig', ['upload_form' => $form]);
    }
}
