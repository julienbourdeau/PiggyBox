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
use Symfony\Component\HttpFoundation\JsonResponse;
use PiggyBox\OrderBundle\Event\OrderEvent;
use PiggyBox\OrderBundle\EventListener\Ordering\OperationListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
        $order_detail->setProduct($product);
		$order_detail->setPrice($em->getRepository('PiggyBoxShopBundle:Price')->find($price_id));
		$order->addOrderDetail($order_detail);

		//persist & flush
        $em->persist($order);
        $em->flush();

		$listener = new OperationListener();
		$dispatcher = new EventDispatcher();
		$dispatcher->addListener('piggy_box_cart.operation_order', array($listener, 'onOperationProcessed'));
		$event = new OrderEvent($order);
		$dispatcher->dispatch('piggy_box_cart.operation_order', $event);
		
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
     * Validate Order
     *
	 * @PreAuthorize("hasRole('ROLE_USER')")
     * @Route("/validation/transaction/{id}", name="validate_order")
     * @Method("POST")
     */
    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('PiggyBoxOrderBundle:Order')->find($id);
		$user = $this->get('security.context')->getToken()->getUser();
		$order->setUser($user);
        $form = $this->createForm(new OrderType(), $order);
        $form->bind($request);

        if ($form->isValid()) {
             // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
			
			// saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirect($this->generateUrl('fos_user_security_logout'));
        }

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
			$data['form'][$order->getId()] = $this->createForm(new OrderType(), $order)->createView();
			}

		return $this->render('PiggyBoxOrderBundle:Order:validate.html.twig', $data);
	}

    /**
	 * Generate the <option> for the openingHour on each select
	 *
     * @Template()
     * @Route(
     *     "horaires/{shop_id}/{time_string}.{_format}",
     *     name="view_opening_hours",
	 *     requirements={"_format"="(json)"},
	 *     options={"expose"=true},
	 *     defaults={"_format"="json"}
     * )
     * @Method({"GET"})
     */
    public function viewOpeningHoursAction(Request $req, $shop_id, $time_string)
    {
        $em = $this->getDoctrine()->getManager();
		$date = new \DateTime($time_string);
		$day_of_the_week = $date->format('N');
		$opening_days = $em->getRepository('PiggyBoxShopBundle:Shop')->find($shop_id)->getOpeningDays();
		$opening_hours = array();

		foreach ($opening_days as $day) {
			if($day->getDayOfTheWeek() == $day_of_the_week){
				
				if($day->getFromTimeMorning() !== null){
					if($day->getToTimeMorning()->format('i')%30 != 0){
						$opening_hours[$day->getFromTimeAfternoon()->format('H:i')] = $day->getFromTimeAfternoon()->format('H:i');
						$day->getFromTimeMorning()->modify(abs(30-$day->getFromTimeMorning()->format('i')).' minutes'); 
					}

					while ( $day->getFromTimeMorning()->format('Hi') < $day->getToTimeMorning()->format('Hi')) {
						$opening_hours[$day->getFromTimeMorning()->format('H:i')] = $day->getFromTimeMorning()->format('H:i');
						$day->getFromTimeMorning()->modify('30 minutes');
					}
				}

				if($day->getFromTimeAfternoon() !== null){
					if($day->getToTimeMorning()->format('i')%30 != 0){
						$opening_hours[$day->getFromTimeAfternoon()->format('H:i')] = $day->getFromTimeAfternoon()->format('H:i');
						$day->getFromTimeMorning()->modify(abs(30-$day->getFromTimeMorning()->format('i')).' minutes'); 
					}

					while ( $day->getFromTimeAfternoon()->format('Hi') < $day->getToTimeAfternoon()->format('Hi')) {
						$opening_hours[$day->getFromTimeAfternoon()->format('H:i')] = $day->getFromTimeAfternoon()->format('H:i');
						$day->getFromTimeAfternoon()->modify('30 minutes');
					}						
				}
			}
		}

        $html = $this->renderView('PiggyBoxOrderBundle:Order:hoursOption.html.twig', array('opening_hours' => $opening_hours));
        return new JsonResponse(array('content' => $html));
    }
}
