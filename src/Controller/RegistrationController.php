<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/registration', name: 'app_registration', methods: ["GET", "POST"])]
    public function index(UserPasswordHasherInterface $userPasswordHasher, Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $registrationForm = $this->createForm(UserRegistrationType::class, $user);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            if($userRepository->findByEmail($registrationForm['email']->getData())) {
                $registrationForm->get('email')->addError(new FormError('This email is already in use.'));

                return $this->render('registration/index.html.twig', [
                    'registration_form' => $registrationForm,
                ]);
            }
            $plaintextPassword = $registrationForm['password']->getData();
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login', ['msg' => 'registration_successful']);
        }


        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'registration_form' => $registrationForm
        ]);
    }
}
