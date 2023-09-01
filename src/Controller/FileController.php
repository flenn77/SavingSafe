<?php

namespace App\Controller;

use DateTime;
use SplFileInfo;
use App\Entity\File;
use App\Form\AddFileType;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;




class FileController extends AbstractController
{
    #[Route('/file', name: 'app_file')]
    public function index(Request $request, EntityManagerInterface $entityManager, FileRepository $fileRepository): Response
    {
        $file = new File();
        $user = $this->getUser();
        
        $form = $this->createForm(AddFileType::class, $file);
        $form->handleRequest($request);
    
        $allFiles = $fileRepository->findBy(['user' => $this->getUser()]);
    
        /**
         * Vérification du formulaire et ajout dans la BDD pour l'ajout de médias
         */
    
         if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['file']->getData();
    
            $originalFileName = $uploadedFile->getClientOriginalName();
            $fileMimeType = $uploadedFile->getMimeType();
    
            $targetDirectory = $this->getParameter('upload_directory');
            $fileName = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($targetDirectory, $fileName);
    
            $movedFile = new SplFileInfo($this->getParameter('upload_directory').'/'.$fileName);
    
            // Informations sur le fichier
            $fileSize = $movedFile->getSize();
    
            $file->setName($originalFileName);
            $file->setSize($fileSize);
            $file->setOwner($user->getEmail());
            $file->setDate(new DateTime());
            $file->setLastAction(new DateTime());
            $file->setFile($fileName);
            $file->setUser($this->getUser());
    
            try {
                $entityManager->persist($file);
                $entityManager->flush();
                $this->addFlash('success', 'Votre fichier a été téléchargé avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier.');
            }
    
            return $this->redirectToRoute('app_file');
        }
    
        return $this->render('file/index.html.twig', [
            'form' => $form->createView(),
            'files' => $allFiles,
        ]);
    }
    




   


#[Route('/file/download/{id}', name: 'file_download')]
public function download(int $id, FileRepository $fileRepository): Response
{
    $fileEntity = $fileRepository->find($id);
    if(!$fileEntity || $fileEntity->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('Fichier non trouvé ou accès non autorisé.');
    }

    $filePath = $this->getParameter('upload_directory').'/'.$fileEntity->getFile();
    
    return $this->file($filePath, $fileEntity->getName());
}


#[Route('/file/delete/{id}', name: 'file_delete', methods: ['POST'])]
public function delete(int $id, FileRepository $fileRepository, EntityManagerInterface $entityManager): Response
{
    $file = $fileRepository->find($id);

    // Si le fichier n'existe pas ou ne appartient pas à l'utilisateur courant
    if (!$file || $file->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('Fichier introuvable ou vous n’avez pas le droit d’accéder à ce fichier.');
    }

    // Supprimer le fichier du serveur
    $filePath = $this->getParameter('upload_directory').'/'.$file->getFile();
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Supprimer le fichier de la base de données
    $entityManager->remove($file);
    $entityManager->flush();

    // Rediriger l'utilisateur vers la liste de ses fichiers avec un message de succès
    $this->addFlash('success', 'Fichier supprimé avec succès.');
    return $this->redirectToRoute('app_file');
}











#[Route('/file/{filename}', name: 'app_file_serve')]
public function serveFile(string $filename): Response
{
    $file = $this->getParameter('upload_directory').'/'.$filename;

    if (!file_exists($file)) {
        throw $this->createNotFoundException('File not found.');
    }

    $response = new BinaryFileResponse($file);
    $response->headers->set('Content-Type', mime_content_type($file));
    $response->headers->set('Content-Disposition', 'inline; filename="'.basename($file).'"');

    return $response;
}






#[Route('/file/view/{id}', name: 'app_file_view')]
public function viewFile($id, FileRepository $fileRepository): Response
{
    $file = $fileRepository->find($id);
    
    if (!$file || $file->getUser() !== $this->getUser()) {
        throw $this->createNotFoundException('File not found or you do not have permission to view it.');
    }

    $isPdf = (pathinfo($file->getFile(), PATHINFO_EXTENSION) === 'pdf');

    return $this->render('file/view.html.twig', [
        'file' => $file,
        'isPdf' => $isPdf,
    ]);
}















}
