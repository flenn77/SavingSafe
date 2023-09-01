<?php

namespace App\Controller;

use App\Form\AccountFormType;
use App\Form\ChangePasswordType;
use App\Model\ChangePasswordModel;
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

        $changePasswordModel = new ChangePasswordModel();

        $profileForm = $this->createForm(AccountFormType::class, $user);
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $changePasswordModel);

        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            // Save the changes
            $entityManager->flush();
        }

        $changePasswordForm->handleRequest($request);
        $isSubmitted = $changePasswordForm->isSubmitted();
        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $isValid = $userPasswordHasher->isPasswordValid($user, $changePasswordModel->old_password);
            
            // dd($changePasswordModel->new_password, $changePasswordModel->confirm_new_password, $changePasswordModel->old_password, $isValid);
            if ($isValid) {
                if ($changePasswordModel->new_password === $changePasswordModel->confirm_new_password) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $changePasswordModel->new_password
                        )
                    );
                    $entityManager->flush();
                } else {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas !');
                }
            } else {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas !');
            }
        }
        

        return $this->render('account/index.html.twig', [
            'profile_form' => $profileForm->createView(),
            'change_password_form' => $changePasswordForm->createView(),
            'isSubmitted' => $isSubmitted,
        ]);
    }
}
