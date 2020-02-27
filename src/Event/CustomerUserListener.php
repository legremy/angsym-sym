<?php

namespace App\Event;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Customer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CustomerUserListener implements EventSubscriberInterface
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setCurrentUserOnCustomer', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function setCurrentUserOnCustomer(ViewEvent $event)
    {
        // On récupère le customer qui est en train d'être créé
        $data = $event->getControllerResult();

        // Si on est en POST et qu'on traite un Customer
        if ($event->getRequest()->getMethod() === "POST" && $data instanceof Customer) {
            // On donne au customer l'utilisateur actuellement connecté
            $data->setUser($this->security->getUser());
        }
    }
}
