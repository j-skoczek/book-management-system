<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\BookSearchType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    #[Route('/book/details/{isbn}', name: 'app_book')]
    public function index(string $isbn, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->findOneBy(['isbn' => $isbn]);
        return $this->render('book/index.html.twig', ['book' => $book]);
    }

    #[Route('/book/delete/{bookId}', name: 'app_book_delete', methods: 'POST')]
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

    #[Route('/book/edit/{id}', name: 'app_book_edit', methods: ["GET", "POST"])]
    public function edit(
        EntityManagerInterface $entityManager,
        Request $request,
        int $id,
        #[Autowire('%photo_dir%')] string $photoDir
    ): Response {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('No book found');
        }

        if ($book->getOwner !== $this->getUser()) {
            throw $this->createNotFoundException('No access');
        }

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

        return $this->render('book/edit.html.twig', [
            'edit_form' => $editForm,
        ]);
    }

    #[Route('/book/upload', name: 'app_book_upload')]
    public function upload(Request $request, UserRepository $userRepository, #[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $book->setOwner($userRepository->findByEmail($user->getUserIdentifier()));
            //todo isbn maybe just treat it as a string and ignore hyphens ? more letters in the field
            // $book->setIsbn(str_replace('-', ));
            if ($cover = $form['coverFileName']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $cover->guessExtension();
                $cover->move($photoDir, $filename);
                $book->setCoverFileName($filename);
            }
            $this->entityManager->persist($book);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('book/upload.html.twig', ['upload_form' => $form]);
    }

    #[Route('/book/list', name: 'app_book_list')]
    public function list(Request $request, BookRepository $bookRepository): Response
    {
        $currentPage = $request->query->getInt('page', 1);

        $searchForm = $this->createForm(BookSearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm['search_by_title_or_author']->getData();
            $paginator = $bookRepository->getSearchPaginator($search, $currentPage);
        }

        return $this->render('book/list.html.twig', [
            'search_form' => $searchForm,
            'paginator' => $paginator ?? $bookRepository->getPaginator($currentPage),
        ]);
    }

    //TODO PUT ALL CRUD FUNCTIONS HERE

    //todo php stan and lint
}
