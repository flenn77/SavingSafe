<?php

namespace App\Controller;

use App\Form\AccountFormType;
use App\Form\ChangePasswordType;
use App\Repository\FileRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();

        $profileForm = $this->createForm(AccountFormType::class, $user);
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $user);

        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            // Save the changes
            $entityManager->flush();
        }

        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $data = $changePasswordForm->getData();

            // Vérifie que les nouveaux mots de passe correspondent
            if ($data['new_password'] === $data['confirm_new_password']) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $data['new_password']
                    )
                );

                $entityManager->flush();
            } else {
                // Ajouter un message flash ou une autre action pour indiquer que les mots de passe ne correspondent pas
                $this->addFlash('error', 'New passwords do not match.');
            }
        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
}
