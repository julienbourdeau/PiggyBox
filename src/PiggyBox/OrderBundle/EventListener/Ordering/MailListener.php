<?php

namespace PiggyBox\OrderBundle\EventListener\Ordering;

use PiggyBox\OrderBundle\Event\OrderEvent;
use PiggyBox\OrderBundle\Entity\Order;
use \Swift_Mailer;

class MailListener
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onOperationProcessed(OrderEvent $event)
    {
        $order = $event->getOrder();

        $message = \Swift_Message::newInstance()
                ->setSubject('[Order]')
                ->setFrom('dev@babelconsulting.fr')
                ->setTo('baptiste.dupuch@babelconsulting.fr')
                ->setBody($this->renderView('PiggyBoxOrderBundle:Order:email.txt.twig'))
            ;
            $this->mailer->send($message);
    }
}
