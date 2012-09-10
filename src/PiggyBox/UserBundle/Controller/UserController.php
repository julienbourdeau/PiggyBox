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
     * Homepage of the users
     *
     * @Route("/", name="user")
     * @Template()
     */
    public function indexAction()
    {
		//NOTE: L'utilisateur doit pouvoir voir les magasins et cliquer sur les magasins
		//NOTE: Il doit pouvoir avoir accès à son panier s'il en a un
		//NOTE: il doit pouvoir se connecter ou s'enregister
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PiggyBoxShopBundle:Shop')->findAll();

        return array(
            'entities' => $entities,
        );
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
            'entity'      => $shop,
			'products'	  => $products	
        );
    }
}
