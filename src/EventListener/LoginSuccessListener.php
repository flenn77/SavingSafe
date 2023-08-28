<?php 

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginSuccessListener {

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException('Vous devez vérifier votre email avant de pouvoir vous connecter.');
        }
    }
}