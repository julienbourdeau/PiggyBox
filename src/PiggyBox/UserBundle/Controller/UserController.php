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
     * @Route("{slug}/{category_title}.{_format}", name="user_show_shop", requirements={"_format"="(html|json)"}, defaults={"_format"="html", "category_title"="default"})
     * @Template()
     */
    public function showAction(Request $req, $slug, $category_title)
    {
		//TODO: Le nom de la route à revoir en fonction de mes lectures REST
        $em = $this->getDoctrine()->getManager();

        $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($slug);
		
		if (!$shop) {
            throw $this->createNotFoundException('Le magasin que vous demandez est introuvable');
        }
		
		if($category_title == "default"){
			$category = $shop->getCategories()->first();
		}
		else{
			$category = $em->getRepository('PiggyBoxShopBundle:Category')->findOneByTitle($category_title);	
		}
		
		if($category->getLevel() == 0){
			$children_categories = $category->getChildren();
			$products = array();
			
			foreach($children_categories as $children_category){
				$products = array_merge($products, 
				$em->getRepository('PiggyBoxShopBundle:Product')->findAllByShopAndCategory($shop->getId(), $children_category->getId())
				);
			}
		}
		else{
			$products = $em->getRepository('PiggyBoxShopBundle:Product')->findAllByShopAndCategory($shop->getId(), $category->getId());
		}

		if ($req->getRequestFormat() === 'json'){
			$html = $this->renderView('PiggyBoxUserBundle:User:tab-products.html.twig', array('products' => $products));
			return new JsonResponse(array('content' => $html));
		}	

        return array(
            'shop'      => $shop,
			'products'	  => $products,
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
