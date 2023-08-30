<?php
 
namespace App\Controller;

use Stripe;
use App\Entity\Users;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
    public function createCharge(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create ([
                "amount" => 20 * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Achat Espace de Stockage"
        ]);

        $userId = $request->getSession()->get('recently_registered_user_id');
        $userFullName = $request->getSession()->get('recently_registered_full_name');

        if ($userId) {
            // Clear the session variable after use
            $request->getSession()->remove('recently_registered_user_id');
            $request->getSession()->remove('recently_registered_full_name');

            // Update the total_space for this user
            $repository = $entityManager->getRepository(Users::class);
            $user = $repository->find($userId);
            if ($user) {
                $currentTotalSpace = $user->getTotalSpace();
                $user->setTotalSpace($currentTotalSpace + 20);
                $entityManager->persist($user);
                $entityManager->flush();

                $email = (new TemplatedEmail())
                    ->from(new Address('savinfsage@flennchante.fr', 'Saving Safe'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de paiement - Saving Safe')
                    ->htmlTemplate('stripe/payment_confirmation.html.twig')
                    ->context([
                        'userFullName' => $userFullName
                    ]);

                $mailer->send($email);
            }
        }

        $this->addFlash(
            'success',
            'Paiement Effectué !'
        );
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }
}