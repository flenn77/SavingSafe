<?php

namespace App\Controller;

use App\Repository\FileRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(UsersRepository $usersRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Seuls les administrateurs ont accès à cette page.');
        }

        $allClients = $usersRepository->findClients();

        return $this->render('client/index.html.twig', [
            'allClients' => $allClients,
        ]);
    }

    #[Route('/client/files', name: 'app_all_client_files')]
    public function viewClientFiles(FileRepository $fileRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Seuls les administrateurs ont accès à cette page.');
        }

        $files = $fileRepository->findAllClientFiles();

        return $this->render('client/all_client_files.html.twig', [
            'files' => $files,
        ]);
    }
}
