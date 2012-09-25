<?php

namespace PiggyBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\UserBundle\Entity\User;
use PiggyBox\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * @Template()
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return array();
    }


    /**
     * Finds and displays a User entity.
     *
     * @Route("{slug}/show", name="user_show_shop")
     * @Template()
     */
    public function showAction(Request $req, $slug)
    {
		//TODO: Le nom de la route à revoir en fonction de mes lectures REST
        $em = $this->getDoctrine()->getManager();

        $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($slug);
		$products = $shop->getProducts();

		$categories = $query = $em->createQuery('SELECT DISTINCT c, p FROM PiggyBoxShopBundle:Category c JOIN c.products p  WHERE p.shop=:id')
					->setParameter('id', $shop->getId())	
					->getResult();

		if (!$shop) {
            throw $this->createNotFoundException('Le magasin que vous demandez est introuvable');
        }

        return array(
            'shop'      => $shop,
			'products'	  => $products,
			'categories'	=> $categories,
        );
    }

    /**
     * @Template()
     * @Route(
     *     "product/{product_id}.{_format}",
     *     name="view_product",
     *     requirements={"_format"="(json)"}
     * )
     * @Method({"GET"})
     */
    public function viewProductPriceAction(Request $req, $product_id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('PiggyBoxShopBundle:Product')->find($product_id);

        $html = $this->renderView('PiggyBoxUserBundle:User:productDetails.html.twig', array('product' => $product));
        return new JsonResponse(array('content' => $html));
    }
	
}
