<?php

namespace App\Event;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreationListener
{
    public function injectDataIntoJWT(JWTCreatedEvent $event)
    {

        $data = $event->getData();
        $data['avatar'] = $event->getUser()->getAvatar();
        $event->setData(($data));
    }
}
