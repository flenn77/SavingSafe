<?php

namespace App\Controller;

use App\Repository\FileRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(UsersRepository $usersRepo, FileRepository $fileRepo): Response
    {
        $filesByUsers = $fileRepo->getFilesCountByUsers();
        $totalFiles = $fileRepo->getTotalFiles();
        $filesUploadedToday = $fileRepo->getFilesUploadedToday();

        $fileFormatsNbr = [
            'pdf' => 10,
            'jpg' => 5,
            'docx' => 7,
            //...
        ];
        
        // ... autres statistiques

        return $this->render('stats/index.html.twig', [
            'totalFiles' => $totalFiles,
            'filesUploadedToday' => $filesUploadedToday,
            'filesByUser' => $filesByUsers,
            'fileFormatsNbr' => $fileFormatsNbr,
            // ... autres statistiques
        ]);
    }
}
