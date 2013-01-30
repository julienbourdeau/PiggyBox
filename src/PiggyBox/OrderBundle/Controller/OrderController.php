<?php

namespace PiggyBox\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\OrderBundle\Entity\Order;
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
     * @Route("/{product_id}", name="cart_add_product", defaults={"_format"="json"}, requirements={"product_id" = "\d+"})
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
     *
     *
     * @Template()
     * @Route("/", name="view_order")
     */
    public function viewOrderAction()
    {
        $seoPage = $this->get('sonata.seo.page');
		$seoPage->setTitle("Mon panier - Côtelettes & Tarte aux Fraises");
		
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
     * @Template("PiggyBoxOrderBundle:Order:viewOrder.html.twig")
     * @Route("/date-heure", name="submit_cart")
     * @Method("POST")
     */
    public function submitCartAction(Request $req)
    {
        $seoPage = $this->get('sonata.seo.page');
		$seoPage->setTitle("Mes disponibilités - Côtelettes & Tarte aux Fraises");
		
        $cart = $this->get('piggy_box_cart.provider')->getCart();
        $em = $this->getDoctrine()->getManager();

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

            foreach ($cart->getOrders() as $order) {
                foreach ($order->getOrderDetail() as $orderDetail) {
                    $this->get('piggy_box_cart.manager.order')->setOrderDetailTotalPrice($orderDetail);
                }
                $this->get('piggy_box_cart.manager.order')->setOrderTotalPrice($order);
            }

            $em->persist($cart);
            $em->flush();
        }

        $cart = $em->getRepository('PiggyBoxOrderBundle:Cart')->findBySession($cart->getId());

        $data['orders'] = $orders = $cart->getOrders();
        $data['form'] =  $this->createForm(new CartType(), $cart)->createView();

        if ($req->request->get('reload') == null) {
            $data['step'] = 'step_date_heure';
        }

        return $data;
    }

    /**
     * Submit Cart for hours details
     *
     * @Template("PiggyBoxOrderBundle:Order:viewOrder.html.twig")
     * @Route("/status/", name="submit_cart_datetime")
     * @Method("POST")
     */
    public function submitCartForDateTimeAction(Request $req)
    {
        $seoPage = $this->get('sonata.seo.page');
		$seoPage->setTitle("Mes disponibilités - Côtelettes & Tarte aux Fraises");
		
        $cart = $this->get('piggy_box_cart.provider')->getCart();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CartType(), $cart);
        $form->bind($req);

        if ($form->isValid()) {

            $em->persist($cart);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('validate_order'));
    }

    /**
     * Submit Cart for hours details
     *
     * @PreAuthorize("hasRole('ROLE_USER')")
     * @Template("PiggyBoxOrderBundle:Order:viewOrder.html.twig")
     * @Route("/validation", name="validate_order")
     */
    public function validationAction(Request $req)
    {
        $seoPage = $this->get('sonata.seo.page');
		$seoPage->setTitle("Méthodes de paiement - Côtelettes & Tarte aux Fraises");
		
        $cart = $this->get('piggy_box_cart.provider')->getCart();

        foreach ($cart->getOrders() as $order) {
            $order->setUser($this->get('security.context')->getToken()->getUser());
            $this->get('piggy_box_cart.manager.order')->changeOrderStatus($order,'toValidate');
            $this->get('piggy_box_cart.manager.order')->removeOrderFromCart($order);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(OrderEvents::ORDER_PASSED, new OrderEvent($order));
        }

        return array('step' => 'step_paiement');
    }

    /**
     * Finally submit and validate the entire Cart
     *
     * @PreAuthorize("hasRole('ROLE_USER')")
     * @Template("PiggyBoxOrderBundle:Order:viewOrder.html.twig")
     * @Route("/confirmation", name="confirm_order")
     */
    public function confirmationAction(Request $req)
    {
        $seoPage = $this->get('sonata.seo.page');
		$seoPage->setTitle("Récapitulatif de ma commande - Côtelettes & Tarte aux Fraises");
		
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $data['orders'] = $em->getRepository('PiggyBoxOrderBundle:Order')->getOrdersByUser($user->getId());

        $data['step'] = 'step_confirmation';

        return $data;
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
