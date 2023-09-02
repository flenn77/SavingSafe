<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(UsersRepository $usersRepository): Response
    {
        $allClients = $usersRepository->findClients();

        // dd($allClients);

        return $this->render('client/index.html.twig', [
            'allClients' => $allClients,
        ]);
    }
}
