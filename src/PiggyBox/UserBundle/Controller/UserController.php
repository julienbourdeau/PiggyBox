<?php

namespace PiggyBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\UserBundle\Entity\User;
use PiggyBox\UserBundle\Form\UserType;

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
		$products = $shop->getProducts()->toArray();


        if (!$shop) {
            throw $this->createNotFoundException('Le magasin que vous demandez est introuvable');
        }

        return array(
            'shop'      => $shop,
			'products'	  => $products	
        );
    }
}
