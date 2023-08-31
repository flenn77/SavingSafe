<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(UserInterface $user, TemplatedEmail $email): void
    {

        $context = $email->getContext();
        $context['tokenUrl'] = "http://127.0.0.1:8000/verify/email?tokenverif=" . $user->getVerifToken();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request): void
    {
        var_dump("test");
        // Récupération du token depuis l'URL
        $tokenFromRequest = $request->get('tokenverif');

        if (!$tokenFromRequest) {
            throw new \Exception('Token absent dans l\'URL.'); // Vous pouvez ici déclencher une exception plus spécifique
        }

        // Récupération de l'utilisateur par le token (assurez-vous d'ajouter cette méthode dans votre Repository)
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['verifToken' => $tokenFromRequest]);

        if (!$user) {
            throw new \Exception('Utilisateur non trouvé avec le token fourni.'); // Vous pouvez ici déclencher une exception plus spécifique
        }

        // Mark the user as verified
        $user->setIsVerified(true);

        // Nettoyer le token
        $user->setVerifToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
