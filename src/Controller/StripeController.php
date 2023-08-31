<?php
 
namespace App\Controller;

use App\Entity\Users;
use App\Entity\Invoice; // Assurez-vous que vous avez cette entité
use Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 
class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }
 
    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, EntityManagerInterface $entityManager)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create([
            "amount" => 20 * 100,
            "currency" => "eur",
            "source" => $request->request->get('stripeToken'),
            "description" => "Achat Espace de Stockage"
        ]);

        $userId = $request->getSession()->get('recently_registered_user_id');

        $repository = $entityManager->getRepository(Users::class);
        $user = null;
        if ($userId) {
            // Clear the session variable after use
            $request->getSession()->remove('recently_registered_user_id');
    
            // Update the total_space for this user
            $user = $repository->find($userId);
            if ($user) {
                $currentTotalSpace = $user->getTotalSpace();
                $user->setTotalSpace($currentTotalSpace + 20);
                $entityManager->persist($user);
                $entityManager->flush();
            }
        }

        if($user) {
            $invoice = new Invoice();
            $invoiceNumber = "INV" . strtoupper(uniqid()); // Générer un numéro de facture unique
            $invoiceDate = new \DateTime(); // Date actuelle

            $invoice->setInvoiceNumber($invoiceNumber)
                    ->setInvoiceDate($invoiceDate)
                    ->setAmount(20)
                    ->setUser($user);

            $entityManager->persist($invoice);
            $entityManager->flush();

            // Redirection vers la page de la facture après la création de la facture
            return $this->redirectToRoute('app_show_invoice', ['id' => $invoice->getId()]);
        } else {
            $this->addFlash(
                'error',
                "Erreur lors de la génération de la facture. L'utilisateur n'a pas été trouvé."
            );
            return $this->redirectToRoute('app_stripe');
        }
    }

    #[Route('/invoice/{id}', name: 'app_show_invoice')]
    public function showInvoice($id, EntityManagerInterface $entityManager): Response
    {
        $invoice = $entityManager->getRepository(Invoice::class)->find($id);

        if (!$invoice) {
            throw $this->createNotFoundException('No invoice found for id ' . $id);
        }

        return $this->render('invoice.html.twig', [
            'invoice' => $invoice
        ]);
    }
}
