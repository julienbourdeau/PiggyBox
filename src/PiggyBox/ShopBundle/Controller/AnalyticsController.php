<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\SecureReturn;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\JsonResponse;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\Shop;

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
   
   /**
     * @Route("/{product_slug}.{_format}", name="outils_product", requirements={"_format"="(json)"}, options={"expose"=true}, defaults={"_format"="json"})
     * @SecureReturn(permissions="VIEW, EDIT")
     */
    public function getProductAction($product_slug)
    {
        if ($this->request->isXmlHttpRequest()) {
            $product = $this->em->getRepository('PiggyBoxShopBundle:Product')->findOneBySlug($product_slug);

            $html = $this->renderView('PiggyBoxShopBundle:Analytics:get_product.html.twig', array('product' => $product));

            return new JsonResponse(array('content' => $html));
        }

        return new JsonResponse(array('content' => null));
    }
}
