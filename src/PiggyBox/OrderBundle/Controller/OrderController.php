<?php

namespace PiggyBox\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Form\OrderType;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PiggyBox\OrderBundle\Entity\Cart;

/**
 * Order controller.
 *
 * @Route("/commande")
 */
class OrderController extends Controller
{

    /**
     * Crée ou récupère un nouvel Order grâce à la session du l'utilisateur 
     *
     * @Route("/ajouter/produit", name="cart_add_product")
     */
    public function addProductAction(Request $req)
    {
        //NOTE: Get the CartProvider that handle the creation/retreive of the cart and session
        $cart = $this->get('piggy_box_cart.provider')->getCart();

        $em = $this->getDoctrine()->getManager();
		$product = $em->getRepository('PiggyBoxShopBundle:Product')->find($req->get('id'));
		$order = $em->getRepository('PiggyBoxOrderBundle:Cart')->getOrderByShop($product->getShop()->getId());
		$order_detail = new OrderDetail();
		
		if(null == $order){
			$order = new Order();
		}

		//Ajout du produit à l'OrderDetail
		$order_detail->setOrder($order);	
        $order_detail->setProduct($product);
		$order_detail->setPrice($em->getRepository('PiggyBoxShopBundle:Price')->find($req->get('price_id')));
		$order->setShop($product->getShop());

        $em->persist($order_detail);
        $em->persist($order);
        $em->flush();

        //NOTE: Set a flash message to share the success
        $this->get('session')->setFlash('success', 'Le produit a ete correctement ajouté');

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }

    /**
     * Remove a product from the cart
     *
     * @Route("/enlever/{productId}", name="cart_remove_product")
     */
    public function removeProductAction($productId)
    {
        //NOTE: Get the CartProvider that handle the creation/retreive of the cart and session
        $cart = $this->get('piggy_box_cart.provider')->getCart();

        //NOTE: Add the product to the cart and save it in DB
        $em = $this->getDoctrine()->getManager();
        $cart->removeProduct($em->getRepository('PiggyBoxCustomerBundle:Product')->find($productId));

        $em->persist($cart);
        $em->flush();

        //NOTE: Set a flash message to share the success
        $this->get('session')->setFlash('success', 'Le produit a ete correctement retiré');

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }

    /**
     * Validate cart 
     *
     * @Route("/validation", name="validate_order")
     */
    public function validateOrderAction(Request $req)
    {
		//NOTE: Get the CartProvider that handle the creation/retreive of the cart and session
        $cart = $this->get('piggy_box_cart.provider')->getOrder();
		$cart->setStatus("sent");
		$cart->setUser($this->get('security.context')->getToken()->getUser());
		//Ajout du produit à l'OrderDetail
        $em = $this->getDoctrine()->getManager();

        $em->persist($cart);
        $em->flush();

        //NOTE: Set a flash message to share the success
        $this->get('session')->setFlash('success', 'Votre commande a bien été envoyé.');

        return new RedirectResponse($this->get('request')->headers->get('referer'));

	}
}
