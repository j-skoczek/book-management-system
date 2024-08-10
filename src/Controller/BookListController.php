<?php

namespace App\Controller;

use App\Form\BookSearchType;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookListController extends AbstractController
{
    #[Route('/book/list', name: 'app_book_list')]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $currentPage = $request->query->getInt('page', 1);

        $searchForm = $this->createForm(BookSearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm['search_by_title_or_author']->getData();
            $paginator = $bookRepository->findByTitleOrAuthorPaginator($search, $currentPage);
        }

        return $this->render('book_list/index.html.twig', [
            'search_form' => $searchForm,
            'paginator' => $paginator ?? $bookRepository->getPaginator($currentPage),
        ]);
    }
}
