<?php

namespace PiggyBox\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\OrderBundle\Entity\Order;
use PiggyBox\OrderBundle\Form\Type\OrderType;
use PiggyBox\OrderBundle\Form\Type\OrderDetailType;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PiggyBox\OrderBundle\Entity\Cart;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Order controller.
 *
 * @Route("/commande")
 */
class OrderController extends Controller
{
    /**
     * Validate Order
     *
     * @Route("/ajouter/produit/{product_id}/{price_id}", name="cart_add_product")
     * @ParamConverter("product", options={"mapping": {"product_id": "id"}})
     * @Method("POST")
     */
    public function addProductToCartAction(Request $req, Product $product, $price_id)
    {
        $orderDetail = new OrderDetail();
        $orderDetail->setProduct($product);
        $em = $this->getDoctrine()->getManager();
        if ($product != Product::WEIGHT_PRICE) {
            $orderDetail->setPrice($em->getRepository('PiggyBoxShopBundle:Price')->find($price_id));
        }

        $form = $this->createForm(new OrderDetailType($product->getPriceType()), $orderDetail);
        $form->bind($req);

        if ($form->isValid()) {
            $order = $this->get('piggy_box_cart.manager.order')->addOrGetOrderFromCart($product->getShop());
            $this->get('piggy_box_cart.manager.order')->addOrderDetailToOrder($order, $orderDetail);

            $em->persist($orderDetail);
            $em->flush();

            $this->get('session')->setFlash('success', 'Le produit a ete correctement ajouté');

            return new RedirectResponse($this->get('request')->headers->get('referer'));
        }

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }

    /**
     * Remove a product from the cart
     *
     * @Route("/enlever/{order_detail_id}", name="cart_remove_product")
     * @ParamConverter("orderDetail", options={"mapping": {"order_detail_id": "id"}})
     */
    public function removeProductAction(OrderDetail $orderDetail)
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
                $order->setStatus('toValidate');
                $cart = $this->get('piggy_box_cart.provider')->getCart();
                $cart->removeOrder($order);

                   $em->persist($cart);
                $em->persist($order);
                $em->flush();

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
						$opening_hours[$day->getFromTimeMorning()->format('H:i')] = $day->getFromTimeMorning()->format('H:i');
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

    /**
     * @Template()
     * @Route("/email", name="email")
     */
    public function emailAction()
    {
        return array();
    }

    /**
     * Validate Order for Shp
     *
     * @PreAuthorize("hasRole('ROLE_SHOP')")
     * @Route("/change/status/{order_id}/{status}", name="change_status")
     */
    public function changeStatusOrderAction(Request $request, $order_id, $status)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('PiggyBoxOrderBundle:Order')->find($order_id);

        $order->setStatus($status);
        $em->persist($order);
        $em->flush();

        return new RedirectResponse($this->get('request')->headers->get('referer'));
    }
}
