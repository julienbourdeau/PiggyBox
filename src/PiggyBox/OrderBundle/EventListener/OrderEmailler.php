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
            if (!in_array($this->container->get('kernel')->getEnvironment(), array('dev', 'test'))) {
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

    public function onOrderPassed(OrderEvent $event)
    {
        $entity = $event->getOrder();

        if ($entity instanceof Order) {
            if (!in_array($this->container->get('kernel')->getEnvironment(), array('dev', 'test'))) {
                $em = $this->container->get('doctrine.orm.default_entity_manager');
                $user = $em->getRepository('PiggyBoxUserBundle:User')->findOneByOwnshop($entity->getShop());

                $message = \Swift_Message::newInstance()
                    ->setSubject('Commande Passée - Côtelettes & Tarte aux Fraises')
                    ->setFrom('lifo@cotelettes-tarteauxfraises.com')
                    ->setTo('contact@cotelettes-tarteauxfraises.com');

                if ($entity->getShop()->getSlug() == 'boucherie-zola') {
                    $message->addBcc( $user->getEmail() );
                }

                $email_body = 'Une commande vient d\'être passé au magasin:'.$entity->getShop()->getName().'.';
                //$email_body .= 'Par l\'utilisateur '.$entity->getUser()->getFirstName().' '.$entity->getUser()->getLastName().'<'.$entity->getUser()->getEmail().'>';
                //$email_body .= 'Telephone: '.$entity->getUser()->getPhone();

                $message->setBody($email_body, 'text/html');

                $this->container->get('mailer')->send($message);
            }
        }
    }
}
