<?php

namespace PiggyBox\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to set the username of the user according to it's email
 */
class RegistrationInitializeListener implements EventSubscriberInterface
{

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialization',
        );
    }

    public function onRegistrationInitialization(UserEvent $event)
    {
        $user = $event->getUser();
        $user->setUsername(uniqid("u", true));
    }
}
