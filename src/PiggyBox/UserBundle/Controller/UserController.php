<?php

namespace PiggyBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\UserBundle\Entity\User;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\MenuDetail;
use PiggyBox\ShopBundle\Entity\Menu;
use PiggyBox\OrderBundle\Form\Type\OrderDetailType;
use PiggyBox\ShopBundle\Form\MenuDetailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Ivory\GoogleMapBundle\Model\MapTypeId;

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
        $seoPage = $this->get('sonata.seo.page');
        //$seoPage->setTitle("Test");
        return array();
    }

    /**
     * @Template()
     * @Route("les-commercants", name="shops")
     */
    public function shopsAction()
    {
        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);

        $map->setCenter(46.875213, -0.296631, true);
        $map->setMapOption('zoom', 8);

        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);

        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '400px'
        ));


        // Configure your marker options
        $marker1 = $this->get('ivory_google_map.marker');
        $marker1->setPrefixJavascriptVariable('marker_');
        $marker1->setPosition(47.2144380, -1.585360, true);

        $marker2 = $this->get('ivory_google_map.marker');
        $marker2->setPrefixJavascriptVariable('marker_');
        $marker2->setPosition(47.215545,-1.564271, true);

        $marker3 = $this->get('ivory_google_map.marker');
        $marker3->setPrefixJavascriptVariable('marker_');
        $marker3->setPosition(47.229044,-1.57163, true);

        $marker4 = $this->get('ivory_google_map.marker');
        $marker4->setPrefixJavascriptVariable('marker_');
        $marker4->setPosition(47.214962,-1.55429, true);

        # Epi de blais
        $marker5 = $this->get('ivory_google_map.marker');
        $marker5->setPrefixJavascriptVariable('marker_');
        $marker5->setPosition(46.594289,0.36257, true);

        # La garenne
        $marker6 = $this->get('ivory_google_map.marker');
        $marker6->setPrefixJavascriptVariable('marker_');
        $marker6->setPosition(46.556027,0.304871, true);

        # Inopinee
        $marker7 = $this->get('ivory_google_map.marker');
        $marker7->setPrefixJavascriptVariable('marker_');
        $marker7->setPosition(46.564791,0.356863, true);

        # Les mimines
        $marker8 = $this->get('ivory_google_map.marker');
        $marker8->setPrefixJavascriptVariable('marker_');
        $marker8->setPosition(47.275619,-1.466761, true);

        $map->addMarker($marker1);
        $map->addMarker($marker2);
        $map->addMarker($marker3);
        $map->addMarker($marker4);
        $map->addMarker($marker5);
        $map->addMarker($marker6);
        $map->addMarker($marker7);
        $map->addMarker($marker8);

        return array('map' => $map);
    }

    /**
     * @Template()
     * @Route("comment-ca-marche", name="ccm")
     */
    public function ccmAction()
    {
        return array();
    }

    /**
     * @Template()
     * @Route("mentions-legales", name="legal")
     */
    public function legalAction()
    {
        return array();
    }

    /**
     * @Template()
     * @Route("qui-sommes-nous", name="about")
     */
    public function aboutAction()
    {
        return array();
    }

    /**
     * Affiche le détail par produit
     *
     * @Route("commerce/{shop_slug}/{category_slug}/{product_slug}", name="view_product_details")
     * @ParamConverter("shop", options={"mapping": {"shop_slug": "slug"}})
     * @Template("PiggyBoxUserBundle:User:showShop.html.twig")
     */
    public function viewProductDetailsAction(Shop $shop, $category_slug, $product_slug)
    {
        $em = $this->getDoctrine()->getManager();
        $data = array();

        $data['product'] = $em->getRepository('PiggyBoxShopBundle:Product')->findOneByShopAndProductSlug($shop->getId(), $product_slug);
        $orderDetail = new OrderDetail();
        $orderDetail->setProduct($data['product']);
        $data['form'][$data['product']->getId()] = $this->createForm(new OrderDetailType(), $orderDetail)->createView();

        $data['similar_products'] = $em->getRepository('PiggyBoxShopBundle:Product')->findBySimilarProductByShopAndByCategory($shop->getId(), $data['product']->getId(), $data['product']->getCategory());
        $data = $this->createOrderDetailForm($data['similar_products'], $data);

        $data['random_products'] = $em->getRepository('PiggyBoxShopBundle:Product')->findByShopExcludeByCategory($shop->getId(), $data['product']->getCategory());
        $data = $this->createOrderDetailForm($data['random_products'], $data);

        $data['shop'] = $shop;
        $data['category_slug'] = $category_slug;

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($shop->getName(), $this->get("router")->generate('user_show_shop', array('shop_slug' => $shop->getSlug())));
        $breadcrumbs->addItem($data['product']->getCategory()->getTitle() , $this->get("router")->generate('user_show_shop', array('shop_slug' => $shop->getSlug(), 'category_title' => $category_slug)));
        $breadcrumbs->addItem($data['product']->getName(), $this->get("router")->generate('view_product_details', array('shop_slug' => $shop->getSlug(), 'category_slug' => $category_slug, 'product_slug' => $product_slug)));

        return $data;
    }

    /**
     * Récupère les produits d'un magasin selon la catégorie
     *
     * @Route("commerce/{shop_slug}/{category_slug}", name="user_show_shop", defaults={"category_slug"="default"})
     * @ParamConverter("shop", options={"mapping": {"shop_slug": "slug"}})
     * @Template()
     */
    public function showShopAction(Request $req, Shop $shop, $category_slug)
    {
        $data = array();
        $data['shop'] = $shop;
        $data['category_slug'] = $category_slug;
        $em = $this->getDoctrine()->getManager();
        $data['menus'] = $em->getRepository('PiggyBoxShopBundle:Menu')->findByShop($shop);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($shop->getName(), $this->get("router")->generate('user_show_shop', array('shop_slug' => $shop->getSlug())));

        if ($category_slug == "default") {
            $data['products'] = $products = $em->getRepository('PiggyBoxShopBundle:Product')->findByActiveProduct($shop->getId());
            $data = $this->createOrderDetailForm($products, $data);

            return $data;
        }

        if ($category_slug == "menus") {
            return $data;
        }

        $category = $em->getRepository('PiggyBoxShopBundle:Category')->findOneBySlug($category_slug);
        $breadcrumbs->addItem($category->getTitle() , $this->get("router")->generate('user_show_shop', array('shop_slug' => $shop->getSlug(), 'category_slug' => $category_slug)));

        if ($category->getLevel() == 0 && $category->getChildren()->count()!=0) {
            $children_categories = $category->getChildren();
            $products = array();

            foreach ($children_categories as $children_category) {
                $products = array_merge($products,
                $em->getRepository('PiggyBoxShopBundle:Product')->findAllByShopAndCategory($shop->getId(), $children_category->getId())
                );
            }
        } else {
            $products = $em->getRepository('PiggyBoxShopBundle:Product')->findAllByShopAndCategory($shop->getId(), $category->getId());
        }
        $data['products'] = $products;
        $data = $this->createOrderDetailForm($products, $data);

        return $data;
    }

    /**
     * Récupère les formules pour laisser choisir l'utilisateur son menu
     *
     * @Route("commerce/{shop_slug}/formule/{menu_slug}/choisir", name="user_show_menus")
     * @ParamConverter("menu", options={"mapping": {"menu_slug": "slug"}})
     * @Template("PiggyBoxUserBundle:User:showShop.html.twig")
     */
    public function createMenuDetailAction(Request $req, Menu $menu)
    {
        $menuDetail = new MenuDetail();
        $menuDetail->setMenu($menu);
        $em = $this->getDoctrine()->getManager();
        $menus = $em->getRepository('PiggyBoxShopBundle:Menu')->findByShop($menu->getShop());
        $products = $menu->getMenuItems()->first()->getProducts()->toArray();

        $form = $this->createForm(new MenuDetailType(), $menuDetail);

        return array(
            'form' => $form->createView(),
            'shop' => $menu->getShop(),
            'menus' => $menus,
            'menu' => $menu,
            'category_slug' => 'menu-detail',
            'products' => $products
        );
    }

    /**
     * Submit the MenuDetail type to get user's choice of menus
     *
     * @Route("/menudetail/{id}", name="user_submit_menus")
     * @ParamConverter("menu", class="PiggyBoxShopBundle:Menu")
     * @Method("POST")
     */
    public function submitMenuDetailAction(Request $req, Menu $menu)
    {
        $menuDetail = new MenuDetail();
        $menuDetail->setMenu($menu);

        $form = $this->createForm(new MenuDetailType(), $menuDetail);
        $form->bind($req);

        if ($form->isValid()) {
            $menuItems = $menu->getMenuItems();

                foreach ($menuItems as $menuItem) {
                    $menuDetail->addProduct($form['products_'.$menuItem->getId()]->getData());
                    $order = $this->get('piggy_box_cart.manager.order')->addOrGetOrderFromCart($menu->getShop());

                    $orderDetail = new OrderDetail();
                    $orderDetail->setProduct($form['products_'.$menuItem->getId()]->getData());
                    $orderDetail->setMenuDetail($menuDetail);
                    $this->get('piggy_box_cart.manager.order')->addOrderDetailToOrder($order, $orderDetail);
                }

            $em = $this->getDoctrine()->getManager();
            $em->persist($menuDetail);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('view_order'));
    }

    private function createOrderDetailForm($products, $data)
    {
        foreach ($products as $product) {
            $orderDetail = new OrderDetail();
            $orderDetail->setProduct($product);
            $data['form'][$product->getId()] = $this->createForm(new OrderDetailType(), $orderDetail)->createView();
        }

        return $data;
    }
}
