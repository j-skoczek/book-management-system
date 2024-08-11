<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
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

    #[Route('/registration', name: 'app_registration')]
    public function index(UserPasswordHasherInterface $userPasswordHasher, Request $request): Response
    {
        $user = new User();
        $registrationForm = $this->createForm(UserRegistrationType::class, $user);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $plaintextPassword = $registrationForm['password']->getData();
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);

            //todo handle roles ? add some ? check update/delete? no idea 
            $user->setRoles(['roles'=> 'ROLE_USER']);

            // todo handle duplicate emails

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            //todo pass some msg like registration successfull? or mobe to login stead of homepage ?
            return $this->redirectToRoute('app_home_page');
        }


        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'registration_form' => $registrationForm
        ]);
    }
}
