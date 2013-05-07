<?php

namespace PiggyBox\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listener responsible to redirect after registration if it comes from an order
 */
class RegistrationSuccessListener implements EventSubscriberInterface
{
    protected $router;

    public function __construct($router)
    {
        $this->router= $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS=> 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getSession()->has('_piggybox.redirect_after_registration')) {
            $isRedirected = $request->getSession()->get('_piggybox.redirect_after_registration');
            $today = new \DateTime('now');
            $today->modify('-5 minutes');

            if ($isRedirected > $today) {
                $url = $this->router->generate('validate_order');
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}
