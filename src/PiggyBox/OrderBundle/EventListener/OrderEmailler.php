<?php

namespace PiggyBox\OrderBundle\EventListener;

use PiggyBox\OrderBundle\Entity\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PiggyBox\OrderBundle\Event\OrderEvent;

class OrderEmailler
{
    private $container;

    /**
     * Inject the whole container because otherwise we would have a circular dependency
     * DBAL -> ORM -> EventManager -> ACL Provider
     * @link https://groups.google.com/d/topic/symfony2/MreLJgfnn_U/discussion
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onOrderValidation(OrderEvent $event)
    {
        $entity = $event->getOrder();

        if ($entity instanceof Order) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Validation de commande - Côtelettes & Tarte aux Fraises')
                ->setFrom('lifo@cotelettes-tarteauxfraises.com')
                ->setTo($entity->getUser()->getEmail());
            $message->addBcc('baptiste@cotelettes-tarteauxfraises.com');

            $header = $message->embed(\Swift_Image::fromPath('../web/bundles/piggyboxorder/img/email-banner-commande-validee.jpg'));
            $tick = $message->embed(\Swift_Image::fromPath('../web/bundles/piggyboxorder/img/tick.png'));
            $mapmarker = $message->embed(\Swift_Image::fromPath('../web/bundles/piggyboxorder/img/mapmarker.png'));
            $highlightmarker = $message->embed(\Swift_Image::fromPath('../web/bundles/piggyboxorder/img/highlightmarker.png'));
            $footer = $message->embed(\Swift_Image::fromPath('../web/bundles/piggyboxorder/img/email-footer.jpg'));

            $message->setBody($this->container->get('templating')->render(
                    'PiggyBoxOrderBundle:Order:email.html.twig',
                    array('order' => $entity,
                          'header' => $header,
                          'tick' => $tick,
                          'mapmarker' => $mapmarker,
                          'highlightmarker' => $highlightmarker,
                          'footer' => $footer,
                        )), 'text/html')
            ;

            $this->container->get('mailer')->send($message);
        }
    }
}
