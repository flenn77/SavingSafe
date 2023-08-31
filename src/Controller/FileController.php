<?php

namespace App\Controller;

use SplFileInfo;
use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileController extends AbstractController
{
    #[Route('/file', name: 'app_file')]
    public function index(Request $request, EntityManagerInterface $entityManager, FileRepository $fileRepository): Response
    {
        // $file = new File();
        // $user = $this->getUser();
        
        // $form = $this->createForm(AddFileType::class, $file);
        // $form->handleRequest($request);

        // $allFiles = $fileRepository->getFiles();

        // /**
        //  * Vérification du formulaire et ajout dans la BDD pour l'ajout de médias
        //  */

        //  if ($form->isSubmitted() && $form->isValid()) {
        //     /** @var UploadedFile $file */
        //     $file = $form['file']->getData();

        //     $originalFileName = $file->getClientOriginalName();
        //     $fileMimeType = $file->getMimeType();

        //     $targetDirectory = $this->getParameter('upload_directory');
        //     $fileName = md5(uniqid()).'.'.$file->guessExtension();
        //     $file->move($targetDirectory, $fileName);

        //     $movedFile = new SplFileInfo($this->getParameter('upload_directory').'/'.$fileName);


        //     // Informations sur le fichier
        //     $fileSize = $movedFile->getSize();

        //     $file->setName($originalFileName);
        //     $file->setSize($fileSize);
        //     $file->setOwner($user->getEmail());
        //     $file->setDate(new DateTime());
        //     $file->setLastAction(new DateTime());
        //     $file->setFile($fileName);
        //     $file->setUser($this->getUser());

        //     $entityManager->persist($file);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_media');
        // }

        return $this->render('file/index.html.twig', [
            'controller_name' => 'FileController',
        ]);
    }
}
