<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    private $pdf;

    // Injectez le service Pdf de KnpSnappyBundle via le constructeur.
    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    // #[Route('/invoice/{id}', name: 'app_show_invoice')]
    // public function showInvoice($id, EntityManagerInterface $entityManager): Response
    // {
    //     $invoice = $entityManager->getRepository(Invoice::class)->find($id);

    //     if (!$invoice) {
    //         throw $this->createNotFoundException('No invoice found for id ' . $id);
    //     }

    //     return $this->render('invoice/show.html.twig', [
    //         'invoice' => $invoice
    //     ]);
    // }



    #[Route('/invoice/{id}', name: 'app_show_invoice')]
    public function showInvoice($id, EntityManagerInterface $entityManager): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);
    
        if (!$invoice) {
            throw $this->createNotFoundException('No invoice found for id ' . $id);
        }
    
        // Récupère l'utilisateur connecté
        $currentUser = $this->getUser();
    
        // Vérifie si l'utilisateur connecté est le propriétaire de la facture
        if ($invoice->getUser() !== $currentUser) {
            // Ajoute un message flash d'erreur
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'accéder à cette facture.');
    
            // Redirige vers la liste des factures de l'utilisateur
            return $this->redirectToRoute('user_invoices');
        }
    
        return $this->render('invoice/invoice.html.twig', [
            'invoice' => $invoice
        ]);
    }
    


    #[Route('/invoice/{id}/pdf', name: 'invoice_pdf')]
public function generatePdf($id, EntityManagerInterface $entityManager): Response
{
    $invoice = $entityManager->getRepository(Invoice::class)->find($id);

    if (!$invoice) {
        throw $this->createNotFoundException('No invoice found for id ' . $id);
    }

    // Récupère l'utilisateur connecté
    $currentUser = $this->getUser();

    // Vérifie si l'utilisateur connecté est le propriétaire de la facture
    if ($invoice->getUser() !== $currentUser) {
        // Ajoute un message flash d'erreur
        $this->addFlash('error', 'Vous n\'avez pas le droit de télécharger cette facture.');

        // Redirige vers la liste des factures de l'utilisateur
        return $this->redirectToRoute('user_invoices');
    }

    $html = $this->renderView('invoice/details.html.twig', [
        'invoice' => $invoice,
    ]);

    $pdfContent = $this->pdf->getOutputFromHtml($html);

    return new Response(
        $pdfContent,
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice_' . $invoice->getInvoiceNumber() . '.pdf"',
        ]
    );
}




    #[Route('/user/invoices', name: 'user_invoices')]
    public function listUserInvoices(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();  // récupérer l'utilisateur actuellement connecté
        $invoices = $entityManager->getRepository(Invoice::class)->findBy(['user' => $user]);
    
        return $this->render('invoice/list.html.twig', [
            'invoices' => $invoices,
        ]);
    }
    





}
