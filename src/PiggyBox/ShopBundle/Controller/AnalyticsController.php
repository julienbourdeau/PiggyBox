<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\SecureReturn;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Analytics controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin/outils")
 */
class AnalyticsController extends Controller
{
    /** @DI\Inject */
    private $request;

    /** @DI\Inject */
    private $router;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $em;

    /**
     * Lister les produits du magasin, les linker vers le CRUD
     *
     * @Route("/", name="outils_index")
     * @SecureReturn(permissions="VIEW, EDIT")
     * @Template()
     */
    public function indexAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();
        $shop = $user->getOwnshop();
        return array('shop' => $shop);
    }

}
