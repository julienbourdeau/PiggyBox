<?php

namespace PiggyBox\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Form\Type\OrderType;
use PiggyBox\OrderBundle\Form\Type\CartType;
use PiggyBox\OrderBundle\Form\Type\OrderDetailType;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PiggyBox\OrderBundle\Entity\Cart;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use PiggyBox\OrderBundle\Event\OrderEvent;
use PiggyBox\OrderBundle\OrderEvents;
/**
 * Order controller.
 *
 * @Route("/checkout")
 */
class OrderController extends Controller
{
    /**
     * Submit OrderDetail
     *
     * @Route("/{product_id}", name="cart_add_product", defaults={"_format"="json"})
     * @ParamConverter("product", options={"mapping": {"product_id": "id"}})
     * @Method("POST")
     */
    public function submitOrderDetailAction(Request $req, Product $product)
    {
        $orderDetail = new OrderDetail();
        $orderDetail->setProduct($product);

        $form = $this->createForm(new OrderDetailType(), $orderDetail);
        $form->bind($req);

        if ($form->isValid()) {
            $order = $this->get('piggy_box_cart.manager.order')->addOrGetOrderFromCart($product->getShop());
            $this->get('piggy_box_cart.manager.order')->addOrderDetailToOrder($order, $orderDetail);

        }
        $html = $this->renderView('PiggyBoxOrderBundle:Order:submitOrderDetail.html.twig', array('orderDetail' => $orderDetail));

        return new JsonResponse(array('content' => $html));
    }

    /**
     * Remove a product from the cart
     *
     * @Route("/{order_detail_id}", name="delete_order_detail")
     * @ParamConverter("orderDetail", options={"mapping": {"order_detail_id": "id"}})
     * @Method("GET|POST")
     */
    public function deleteOrderDetailAction(Request $req, OrderDetail $orderDetail)
    {
        $order = $orderDetail->getOrder();
        $this->get('piggy_box_cart.manager.order')->removeOrderDetailFromOrder($order, $orderDetail);

        if (0 == $order->getOrderDetail()->count()) {
            $this->get('piggy_box_cart.manager.order')->removeOrderFromCart($order);
            $this->get('piggy_box_cart.manager.order')->removeOrder($order);
        }

        $this->get('session')->setFlash('success', 'Le produit a ete correctement retiré');

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Show the cart to the people
     *
     * @Template()
     * @Route("/", name="view_order")
     */
    public function viewOrderAction()
    {
        $cart = $this->get('piggy_box_cart.provider')->getCart();
        $em = $this->getDoctrine()->getManager();
        $cart = $em->getRepository('PiggyBoxOrderBundle:Cart')->findBySession($cart->getId());

        $data['orders'] = $orders = $cart->getOrders();
        $data['form'] =  $this->createForm(new CartType(), $cart)->createView();

        return $data;
    }

    /**
     * Submit OrderDetail
     *
     * @Route("/cart/test", name="submit_cart")
     * @Method("POST")
     */
    public function submitCartAction(Request $req)
    {
        $cart = $this->get('piggy_box_cart.provider')->getCart();

		foreach ($cart->getOrders() as $order) {
    		foreach ($order->getOrderDetail() as $orderDetail) $originalOrderDetails[] = $orderDetail;
		}

        $form = $this->createForm(new CartType(), $cart);
        $form->bind($req);
		
        if ($form->isValid()) {

			foreach ($cart->getOrders() as $order) {
				foreach ($order->getOrderDetail() as $orderDetail) {
					foreach ($originalOrderDetails as $key => $toDel) {
						if ($toDel->getId() === $orderDetail->getId()) {
							unset($originalOrderDetails[$key]);
						}
					}
				}
			}

            foreach ($originalOrderDetails as $orderDetail) {
		        $this->get('piggy_box_cart.manager.order')->removeOrderDetailFromOrder($orderDetail->getOrder(), $orderDetail);

		        if (0 == $orderDetail->getOrder()->getOrderDetail()->count()) {
        		    $this->get('piggy_box_cart.manager.order')->removeOrderFromCart($orderDetail->getOrder());
         		   	$this->get('piggy_box_cart.manager.order')->removeOrder($orderDetail->getOrder());
        		}
				
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);
            $em->flush();
        }

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
            try {
                $this->get('piggy_box_cart.manager.order')->removeOrderFromCart($order);
                $this->get('piggy_box_cart.manager.order')->changeOrderStatus($order, 'toValidate');

                $this->get('session')->getFlashBag()->set('success', 'La commande a été envoyé au commerçant');

                return $this->redirect($this->generateUrl('shops'));
            } catch (\Exception $e) {
                $this->get('logger')->crit($e->getMessage(), array('exception', $e));
                $this->get('session')->getFlashBag()->set('error', 'Une erreur est survenue, notre équipe a été prévenue');
            }
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
     * @ParamConverter("shop", options={"mapping": {"shop_id": "id"}})
     * @Method({"GET"})
     */
    public function viewOpeningHoursAction(Request $req, Shop $shop, $time_string)
    {
        $date = new \DateTime($time_string);

        $opening_hours  = $this->get('piggy_box_cart.manager.order')->getOpeningHoursFromShopForDay($shop, $date);

        $html = $this->renderView('PiggyBoxOrderBundle:Order:hoursOption.html.twig', array('opening_hours' => $opening_hours));

        return new JsonResponse(array('content' => $html));
    }

    /**
     * Validation process for orders
     *
     * @PreAuthorize("hasRole('ROLE_SHOP')")
     * @Route("/change/status/{order_id}/{status}", name="change_status")
     * @ParamConverter("order", options={"mapping": {"order_id": "id"}})
     */
    public function changeOrderStatusAction(Request $request, Order $order, $status)
    {
        $this->get('piggy_box_cart.manager.order')->changeOrderStatus($order, $status);

        if ($status == 'toPrepare') {
            $dispatcher = $this->get('event_dispatcher');
               $dispatcher->dispatch(OrderEvents::ORDER_VALIDATED, new OrderEvent($order));
        }

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }
}
