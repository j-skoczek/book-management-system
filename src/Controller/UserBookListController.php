<?php

namespace App\Controller;

use App\Form\BookSearchType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserBookListController extends AbstractController
{
    public function __construct(private Security $security) {}

    #[Route('/user/book/list', name: 'app_user_book_list')]
    public function index(Request $request, BookRepository $bookRepository, UserRepository $userRepository): Response
    {
        if ($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('app_home_page');
        }

        $user = $this->security->getUser();
        $userEntity = $userRepository->findByEmail($user->getUserIdentifier());
        $currentPage = $request->query->getInt('page', 1);

        $searchForm = $this->createForm(BookSearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm['search_by_title_or_author']->getData();
            $paginator = $bookRepository->getUserSearchPaginator($search, $userEntity, $currentPage);
        }

        return $this->render('user_book_list/index.html.twig', [
            'search_form' => $searchForm,
            'paginator' => $paginator ?? $bookRepository->getUserPaginator($userEntity, $currentPage),
        ]);
    }
}
