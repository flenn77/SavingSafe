<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Invoice;
use Stripe;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StoragePurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase_storage')]
    public function index(): Response
    {
        return $this->render('storage_purchase/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }

    #[Route('/purchase/charge', name: 'app_purchase_storage_charge', methods: ['POST'])]
    public function createChargePurchase(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create([
            "amount" => 20 * 100,
            "currency" => "eur",
            "source" => $request->request->get('stripeToken'),
            "description" => "Achat 20Go Espace de Stockage"
        ]);

        $user = $this->getUser();
        $currentTotalSpace = $user->getTotalSpace();
        $user->setTotalSpace($currentTotalSpace + 20);

        $entityManager->persist($user);
        
        $invoice = $this->createInvoiceP($user, 20, $entityManager);

        $email = (new TemplatedEmail())
            ->from(new Address('savinfsage@flennchante.fr', 'Saving Safe'))
            ->to($user->getEmail())
            ->subject('Confirmation de paiement - Saving Safe')
            ->htmlTemplate('storage_purchase/payment_confirmation.html.twig')
            ->context([
                'userFullName' => $user->getFullName()
            ]);

        $mailer->send($email);

        return $this->redirectToRoute('app_show_invoice', ['id' => $invoice->getId()]);
    }

    private function createInvoiceP(Users $user, float $amount, EntityManagerInterface $entityManager): Invoice
    {
        $invoice = new Invoice();
        $invoiceNumber = "INV" . strtoupper(uniqid());
        $invoiceDate = new \DateTime();

        $invoice->setInvoiceNumber($invoiceNumber)
                ->setInvoiceDate($invoiceDate)
                ->setAmount($amount)
                ->setUser($user);

        $entityManager->persist($invoice);
        $entityManager->flush();

        return $invoice;
    }
}
