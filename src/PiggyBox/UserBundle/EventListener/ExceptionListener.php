<?php

namespace PiggyBox\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use \Swift_Mailer;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListener
{
    protected $mailer;

	protected $kernel;
	
    public function __construct(\Swift_Mailer $mailer, KernelInterface $kernel)
    {
        $this->mailer = $mailer;
		$this->kernel = $kernel;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
		if (in_array($this->kernel->getEnvironment(), array('prod'))) {
		$message = \Swift_Message::newInstance()
		        ->setSubject('[Exception]')
				->setFrom('dev@babelconsulting.fr')
				->setTo('baptiste.dupuch@babelconsulting.fr')
		        ->setBody($event->getException())
		    ;
		    $this->mailer->send($message);
		}
    }
}
