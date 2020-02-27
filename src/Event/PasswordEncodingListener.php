<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncodingListener implements EventSubscriberInterface
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            "kernel.request" => "convertUserPassword"
        ];

        // Pour ajouter une priorité
        // return [
        //     "kernel.request" => ["convertUserPassword", 3000]
        // ];
    }

    public function convertUserPassword(RequestEvent $event)
    {
        $data = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();

        if ($data && $data instanceof User && $method == "POST" && $data->getPassword() !== "") {
            $plainPass = $data->getPassword("password");
            $hash = $this->encoder->encodePassword($data, $plainPass);
            $data->setPassword($hash);
        }
    }
}
