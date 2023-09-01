<?php

namespace App\Controller;

use App\Form\AccountFormType;
use App\Form\ChangePasswordType;
use App\Model\ChangePasswordModel;
use App\Repository\FileRepository;
use App\Repository\UsersRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
        if ($profileForm->isSubmitted()) {
            if ($profileForm->isValid()) {
                // Save the changes
                $entityManager->flush();
                $this->addFlash('success', 'Vos informations de profil ont été mises à jour avec succès.');
            } else {
                $this->addFlash('error', 'Il y a eu une erreur lors de la mise à jour de votre profil.');
            }
        }

        $changePasswordForm->handleRequest($request);
        if ($changePasswordForm->isSubmitted()) {
            if ($changePasswordForm->isValid()) {
                $isValid = $userPasswordHasher->isPasswordValid($user, $changePasswordModel->old_password);

                if ($isValid) {
                    if ($changePasswordModel->new_password === $changePasswordModel->confirm_new_password) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $changePasswordModel->new_password
                            )
                        );
                        $entityManager->flush();
                        $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
                    } else {
                        $this->addFlash('error', 'Les mots de passe fournis ne correspondent pas.');
                    }
                } else {
                    $this->addFlash('error', 'Votre ancien mot de passe est incorrect.');
                }
            } else {
                $this->addFlash('error', 'Il y a eu une erreur lors du changement de votre mot de passe.');
            }
        }

        return $this->render('account/index.html.twig', [
            'profile_form' => $profileForm->createView(),
            'change_password_form' => $changePasswordForm->createView(),
        ]);
    }

    #[Route('/delete_account', name: 'app_account_delete')]
    public function deleteAccount(EntityManagerInterface $entityManager, FileRepository $fileRepository, InvoiceRepository $invoiceRepository, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user = $this->getUser();
        if (!$user) {
            // L'utilisateur n'est pas connecté; redirigez vers la page de connexion ou autre.
            return $this->redirectToRoute('login_route');
        }

        // Supprimer les fichiers et les factures associés à cet utilisateur
        $fileRepository->deleteFilesForUser($user);
        $invoiceRepository->deleteInvoicesForUser($user); // Supposons que vous ayez un InvoiceRepository

        // Supprimer le compte utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnecter l'utilisateur et détruire la session
        $tokenStorage->setToken(null);
        $session->invalidate();

        $this->addFlash('success', 'Votre compte a été supprimé.');

        return $this->redirectToRoute('app_login');
    }
}
