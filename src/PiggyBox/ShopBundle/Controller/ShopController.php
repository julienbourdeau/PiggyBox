<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\ShopBundle\Entity\Day;
use PiggyBox\ShopBundle\Form\ShopType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use JMS\SecurityExtraBundle\Annotation\SecureReturn;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Shop controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin")
 */
class ShopController extends Controller
{
    /**
     * Homepage of the shop-owner. The goal of this page is receive order and to link all the other page
     *
     * @SecureReturn(permissions="VIEW")
     * @Route("/.{_format}", name="moncommerce_mescommandes", requirements={"_format"="(html|json)"}, defaults={"_format"="html"})
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $data = array();

        $data['orders_toValidate']	= $em->createQuery('SELECT o, u, d , p FROM PiggyBoxOrderBundle:Order o LEFT JOIN o.user u LEFT JOIN o.order_detail d LEFT JOIN d.product p WHERE (o.shop=:shop_id AND o.status=\'toValidate\') ORDER BY o.pickupatDate, o.pickupatTime ASC')
                                  ->setParameter('shop_id', $user->getOwnShop()->getId())
                                  ->getResult();

        if ($this->get('request')->getRequestFormat() == 'json') {
            $html = $this->renderView('PiggyBoxShopBundle:Shop:orders_to_validate.html.twig', $data);

            return new JsonResponse(array('content' => $html));
        }

        $data['orders_toPrepare']	= $em->createQuery('SELECT o, u, d , p FROM PiggyBoxOrderBundle:Order o LEFT JOIN o.user u LEFT JOIN o.order_detail d LEFT JOIN d.product p WHERE (o.shop=:shop_id AND o.status=\'toPrepare\') ORDER BY o.pickupatDate, o.pickupatTime ASC')
                                  ->setParameter('shop_id', $user->getOwnShop()->getId())
                                  ->getResult();

        $data['orders_toArchive']	= $em->createQuery('SELECT o, u, d , p FROM PiggyBoxOrderBundle:Order o LEFT JOIN o.user u LEFT JOIN o.order_detail d LEFT JOIN d.product p WHERE (o.shop=:shop_id AND o.status=\'toArchive\') ORDER BY o.pickupatDate, o.pickupatTime ASC')
                                  ->setParameter('shop_id', $user->getOwnShop()->getId())
                                  ->getResult();

        return $data;
    }

    /**
     * Displays a form to create a new Shop entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/new", name="moncommerce_new")
     * @Template()
     */
    public function newAction()
    {
        //NOTE: Méthode permettant de créer un nouveau magasin avec les ACL de l'utilisateur avec le ROLE_ADMIN
        //TODO: Ajouter plus de détails au magasin que le nom et type...
        $shop = new Shop();

        // Ajout de tous les jours de la semaine
        $monday = new Day();
        $monday->setDayOfTheWeek(1);

        $tuesday = new Day();
        $tuesday->setDayOfTheWeek(2);

        $wednesday = new Day();
        $wednesday->setDayOfTheWeek(3);

        $thursday = new Day();
        $thursday->setDayOfTheWeek(4);

        $friday = new Day();
        $friday->setDayOfTheWeek(5);

        $saturday = new Day();
        $saturday->setDayOfTheWeek(6);

        $sunday = new Day();
        $sunday->setDayOfTheWeek(7);



        $shop->addOpeningDay($monday);
        $shop->addOpeningDay($tuesday);
        $shop->addOpeningDay($wednesday);
        $shop->addOpeningDay($thursday);
        $shop->addOpeningDay($friday);
        $shop->addOpeningDay($saturday);
        $shop->addOpeningDay($sunday);


        $form = $this->createForm(new ShopType(), $shop);

        return array(
            'entity' => $shop,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Shop entity.
     *
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/create", name="moncommerce_create")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Shop:new.html.twig")
     */
    public function createAction(Request $request)
    {
        //NOTE: Vérification de la validité du formulaire > Ajout du Shop à l'utilisateur > création des ACL à l'objet $user > Ajout du ROLE_SHOP et suppression du ROLE_ADMIN > Redirection vers la route logout
        $shop = new Shop();
        $form = $this->createForm(new ShopType(), $shop);
        $form->bind($request);

        if ($form->isValid()) {
             // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();

            // saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
            $user->setOwnshop($shop);
            $em->flush();

            // creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($shop);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);

            // Ajout du ROLE_SHOP et suppression du ROLE_ADMIN
            $manipulator = $this->get('fos_user.util.user_manipulator');
            $manipulator->addRole($user,"ROLE_SHOP");
            $manipulator->removeRole($user,"ROLE_ADMIN");

            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }

        return array(
            'entity' => $shop,
            'form'   => $form->createView(),
        );
    }
}
