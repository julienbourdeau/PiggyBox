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

		//NOTE: Get the product to add
        $em = $this->getDoctrine()->getManager();
		$product = $em->getRepository('PiggyBoxShopBundle:Product')->find($req->get('id'));

		$orders = $cart->getOrders()->toArray();
		$shop = $product->getShop();
		$order = null;

		foreach($orders as $tab){
			if($shop->getId() == $tab->getShop()->getId()){
				$order = $tab;	
				break;
			}
		}
	
		if(null == $order){
			$order = new Order();
			$cart->addOrder($order);
    	    $em->persist($cart);	
		}
		$order->setShop($product->getShop());

		//Ajout du produit à l'OrderDetail
		$order_detail = new OrderDetail();
		$order_detail->setOrder($order);	
        $order_detail->setProduct($product);
		$order_detail->setPrice($em->getRepository('PiggyBoxShopBundle:Price')->find($req->get('price_id')));

		//persist & flush
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
     * @Route("/validation/{order_id}/transaction", name="validate_order")
     */
    public function validateOrderAction(Request $req, $order_id)
    {
		//Récupérer l'Order
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('PiggyBoxOrderBundle:Order')->find($order_id);

		//Récupérer le Cart pour retirer l'Order du Cart
        $cart = $this->get('piggy_box_cart.provider')->getCart();
		$cart->removeOrder($order);		
		
		$order->setStatus("toValidate");
		$order->setUser($this->get('security.context')->getToken()->getUser());

        $em->persist($order);
        $em->persist($cart);
        $em->flush();
		
        //NOTE: Set a flash message to share the success
        $this->get('session')->setFlash('success', 'Votre commande a bien été envoyé.');

        return new RedirectResponse($this->get('request')->headers->get('referer'));

	}

    /**
     * Validation Page
     *
     * @Route("/validation", name="validation_page")
     */
	public function validationPageAction()
	{
		return $this->render('PiggyBoxOrderBundle:Order:validate.html.twig');
	}
}
