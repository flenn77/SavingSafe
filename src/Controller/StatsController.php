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

        $fileNames = $fileRepo->getAllFileNames();

        // Map des extensions aux catégories
        $extensionsMap = [
            'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
            'images' => ['png', 'jpeg', 'jpg', 'gif', 'bmp', 'webp', 'svg', 'tiff'],
            'audios' => ['mp3', 'wav'],
            'vidéos' => ['mp4', 'avi', 'mov'],
            'archives' => ['zip', 'rar', 'tar', '7z'],
        ];

        $fileFormatsNbr = [
            'documents' => 0,
            'images' => 0,
            'audios' => 0,
            'vidéos' => 0,
            'archives' => 0,
            'autres' => 0
        ];        
        
        foreach ($fileNames as $file) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $recognized = false;
        
            foreach ($extensionsMap as $category => $extensions) {
                if (in_array($extension, $extensions)) {
                    $fileFormatsNbr[$category]++;
                    $recognized = true;
                    break; // quitte la boucle intérieure dès qu'une correspondance est trouvée
                }
            }
        
            if (!$recognized) {
                $fileFormatsNbr['autres']++;
            }
        }

        $fileFormatsNbr = array_filter($fileFormatsNbr, function ($value) {
            return $value !== 0;
        });

        $fileFormatsNbrKeys = array_keys($fileFormatsNbr);
        $fileFormatsNbrValues = array_values($fileFormatsNbr);

        return $this->render('stats/index.html.twig', [
            'totalFiles' => $totalFiles,
            'filesUploadedToday' => $filesUploadedToday,
            'filesByUser' => $filesByUsers,
            'fileFormatsNbrKeys' => $fileFormatsNbrKeys,
            'fileFormatsNbrValues' => $fileFormatsNbrValues,
        ]);
    }
}
