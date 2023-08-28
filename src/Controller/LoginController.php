<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage): Response
    {
        if ($this->getUser()) {
            if (!$this->getUser()->isVerified()) {
                $tokenStorage->setToken(null); // déconnectez l'utilisateur
                $this->addFlash('warning', 'Vous devez vérifier votre email avant de pouvoir vous connecter.');
                return $this->redirectToRoute('app_login');
            }

            return $this->redirectToRoute('app_account');
        }    

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
