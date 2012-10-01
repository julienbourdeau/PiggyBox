<?php

namespace PiggyBox\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use \Swift_Mailer;

class ExceptionListener
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
		$message = \Swift_Message::newInstance()
		        ->setSubject('Hello Email')
				->setFrom('dev@babelconsulting.fr')
				->setTo('baptiste.dupuch@babelconsulting.fr')
		        ->setBody($event->getException())
		    ;
		    $this->mailer->send($message);
    }
}
