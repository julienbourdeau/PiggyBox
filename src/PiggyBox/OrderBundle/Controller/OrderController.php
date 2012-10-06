<?php

namespace PiggyBox\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Form\Type\OrderType;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PiggyBox\OrderBundle\Entity\Cart;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

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
     * @Route("/ajouter/produit/{product_id}/{price_id}", name="cart_add_product")
     */
    public function addProductAction(Request $req, $product_id, $price_id)
    {
        //NOTE: Get the CartProvider that handle the creation/retreive of the cart and session
        $cart = $this->get('piggy_box_cart.provider')->getCart();

		//NOTE: Get the product to add
        $em = $this->getDoctrine()->getManager();
		$product = $em->getRepository('PiggyBoxShopBundle:Product')->find($product_id);

		$orders = $cart->getOrders()->toArray();
		$shop = $product->getShop();
		$order = null;

		foreach($orders as $tab){
			if($shop->getId() == $tab->getShop()->getId()){
				$order = $tab;	
				break;
			}
		}
	
		//Création d'un nouvel order si nouveau produit
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
		$order_detail->setPrice($em->getRepository('PiggyBoxShopBundle:Price')->find($price_id));

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
     * @Route("/enlever/{order_detail_id}", name="cart_remove_product")
     */
    public function removeProductAction($order_detail_id)
    {
		//trouver le magasin du produit
        $em = $this->getDoctrine()->getManager();
		$order_detail = $em->getRepository('PiggyBoxOrderBundle:OrderDetail')->find($order_detail_id); 

		$order = $order_detail->getOrder();
		//TODO: Check the order_detail quantity
		$order->removeOrderDetail($order_detail);

		if(0 == $order->getOrderDetail()->count()){
	        $cart = $this->get('piggy_box_cart.provider')->getCart();
			$cart->removeOrder($order);	
			$em->remove($order);
			$em->persist($cart);
		}
		
		//Remove OrderDetail
		$em->remove($order_detail);
		$em->persist($order);
		$em->flush();

        //NOTE: Set a flash message to share the success
        $this->get('session')->setFlash('success', 'Le produit a ete correctement retiré');

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }

    /**
     * Validate cart 
   	 * @PreAuthorize("hasRole('ROLE_USER')")
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
     * Validate Order
     *
	 * @PreAuthorize("hasRole('ROLE_USER')")
     * @Route("/validation/transaction", name="validate_order")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $order = new Order();
        $form = $this->createForm(new OrderType(null), $order);
        $form->bind($request);
		var_dump($request->request);

        if ($form->isValid()) {
			var_dump("success");die();
             // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
			
			// saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
			$user->setOwnshop($shop);
            $em->flush();

            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }
			var_dump("success");die();
        return array(
            'entity' => $order,
            'form'   => $form->createView(),
        );
    }

    /**
     * Validation Page
     *
	 * @PreAuthorize("hasRole('ROLE_USER')")
     * @Route("/validation", name="validation_page")
     */
	public function validationPageAction()
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$cart = $this->get('piggy_box_cart.provider')->getCart();
		$orders = $cart->getOrders();
		
		$data = array();
		
		foreach ($orders as $order) {
			$order->setUser($user);
			$shop_id = $order->getShop()->getId();
			$data['form'][$order->getId()] = $this->createForm(new OrderType($shop_id), $order)->createView();
		}

		return $this->render('PiggyBoxOrderBundle:Order:validate.html.twig', $data);
	}
}
