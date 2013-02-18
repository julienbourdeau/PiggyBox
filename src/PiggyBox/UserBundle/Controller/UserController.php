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
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - La commande en ligne pour vos commerces de proximité");

        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);

        $map->setCenter(46.875213, -0.296631, true);
        $map->setMapOption('zoom', 8);

        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);

        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '500px'
        ));

        # zola
        $marker1 = $this->get('ivory_google_map.marker');
        $marker1->setPrefixJavascriptVariable('marker_');
        $marker1->setPosition(47.214048, -1.585698, true);

        $event1 = $this->get('ivory_google_map.event');
        $event1->setInstance($marker1->getJavascriptVariable());
        $event1->setEventName('click');
        $event1->setHandle('function(){showShopInMap("boucherie-zola")}');

        # le boulanger de zola
        $marker1bis = $this->get('ivory_google_map.marker');
        $marker1bis->setPrefixJavascriptVariable('marker_');
        $marker1bis->setPosition(47.214061, -1.585741, true);
        $markerImage1bis = $this->get('ivory_google_map.marker_image');
        $markerImage1bis->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker1bis->setIcon($markerImage1bis);

        $event1bis = $this->get('ivory_google_map.event');
        $event1bis->setInstance($marker1bis->getJavascriptVariable());
        $event1bis->setEventName('click');
        $event1bis->setHandle('function(){showShopInMap("le-boulanger-de-zola")}');

        # copernic
        $marker2 = $this->get('ivory_google_map.marker');
        $marker2->setPrefixJavascriptVariable('marker_');
        $marker2->setPosition(47.215545,-1.564271, true);

        $event2 = $this->get('ivory_google_map.event');
        $event2->setInstance($marker2->getJavascriptVariable());
        $event2->setEventName('click');
        $event2->setHandle('function(){showShopInMap("boucherie-copernic")}');

        # gourmets
        $marker3 = $this->get('ivory_google_map.marker');
        $marker3->setPrefixJavascriptVariable('marker_');
        $marker3->setPosition(47.229044,-1.57163, true);

        $event3 = $this->get('ivory_google_map.event');
        $event3->setInstance($marker3->getJavascriptVariable());
        $event3->setEventName('click');
        $event3->setHandle('function(){showShopInMap("boucherie-des-gourmets")}');

        # bouffay
        $marker4 = $this->get('ivory_google_map.marker');
        $marker4->setPrefixJavascriptVariable('marker_');
        $marker4->setPosition(47.214962,-1.55429, true);

        $event4 = $this->get('ivory_google_map.event');
        $event4->setInstance($marker4->getJavascriptVariable());
        $event4->setEventName('click');
        $event4->setHandle('function(){showShopInMap("boucherie-du-bouffay")}');

        # Epi de blais
        $marker5 = $this->get('ivory_google_map.marker');
        $marker5->setPrefixJavascriptVariable('marker_');
        $marker5->setPosition(46.594289,0.36257, true);

        $event5 = $this->get('ivory_google_map.event');
        $event5->setInstance($marker5->getJavascriptVariable());
        $event5->setEventName('click');
        $event5->setHandle('function(){showShopInMap("banette-buxerolles")}');
        $markerImage5 = $this->get('ivory_google_map.marker_image');
        $markerImage5->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker5->setIcon($markerImage5);

        # La garenne
        $marker6 = $this->get('ivory_google_map.marker');
        $marker6->setPrefixJavascriptVariable('marker_');
        $marker6->setPosition(46.556027,0.304871, true);
        $markerImage6 = $this->get('ivory_google_map.marker_image');
        $markerImage6->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker6->setIcon($markerImage6);

        $event6 = $this->get('ivory_google_map.event');
        $event6->setInstance($marker6->getJavascriptVariable());
        $event6->setEventName('click');
        $event6->setHandle('function(){showShopInMap("banette-la-garenne")}');

        # Inopinee
        $marker7 = $this->get('ivory_google_map.marker');
        $marker7->setPrefixJavascriptVariable('marker_');
        $marker7->setPosition(46.564791,0.356863, true);
        $markerImage7 = $this->get('ivory_google_map.marker_image');
        $markerImage7->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker7->setIcon($markerImage7);

        $event7 = $this->get('ivory_google_map.event');
        $event7->setInstance($marker7->getJavascriptVariable());
        $event7->setEventName('click');
        $event7->setHandle('function(){showShopInMap("banette-grand-large")}');

        # Les mimines
        $marker8 = $this->get('ivory_google_map.marker');
        $marker8->setPrefixJavascriptVariable('marker_');
        $marker8->setPosition(47.275619,-1.466761, true);
        $markerImage8 = $this->get('ivory_google_map.marker_image');
        $markerImage8->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker8->setIcon($markerImage8);

        $event8 = $this->get('ivory_google_map.event');
        $event8->setInstance($marker8->getJavascriptVariable());
        $event8->setEventName('click');
        $event8->setHandle('function(){showShopInMap("banette-la-mimine")}');

        # Futuroscope
        $marker9 = $this->get('ivory_google_map.marker');
        $marker9->setPrefixJavascriptVariable('marker_');
        $marker9->setPosition(46.660674,0.363318, true);
        $markerImage9 = $this->get('ivory_google_map.marker_image');
        $markerImage9->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker9->setIcon($markerImage8);

        $event9 = $this->get('ivory_google_map.event');
        $event9->setInstance($marker9->getJavascriptVariable());
        $event9->setEventName('click');
        $event9->setHandle('function(){showShopInMap("banette-futuroscope")}');

        $map->addMarker($marker1);
        $map->addMarker($marker1bis);
        $map->addMarker($marker2);
        $map->addMarker($marker3);
        $map->addMarker($marker4);
        $map->addMarker($marker5);
        $map->addMarker($marker6);
        $map->addMarker($marker7);
        $map->addMarker($marker8);
        $map->addMarker($marker9);

        // It can only be used with a DOM event
        // By default, the capture flag is false
        $event1->setCapture(true);
        $event1bis->setCapture(true);
        $event2->setCapture(true);
        $event3->setCapture(true);
        $event4->setCapture(true);
        $event5->setCapture(true);
        $event6->setCapture(true);
        $event7->setCapture(true);
        $event8->setCapture(true);
        $event9->setCapture(true);

        // Add a DOM event
        $map->getEventManager()->addDomEvent($event1);
        $map->getEventManager()->addDomEvent($event1bis);
        $map->getEventManager()->addDomEvent($event2);
        $map->getEventManager()->addDomEvent($event3);
        $map->getEventManager()->addDomEvent($event4);
        $map->getEventManager()->addDomEvent($event5);
        $map->getEventManager()->addDomEvent($event6);
        $map->getEventManager()->addDomEvent($event7);
        $map->getEventManager()->addDomEvent($event8);
        $map->getEventManager()->addDomEvent($event9);

        return array('map' => $map);
    }

    /**
     * @Template()
     * @Route("les-commercants", name="shops")
     */
    public function shopsAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Rechercher des commerçants");

        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);

        $map->setCenter(46.875213, -0.296631, true);
        $map->setMapOption('zoom', 8);

        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);

        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '500px'
        ));

        # zola
        $marker1 = $this->get('ivory_google_map.marker');
        $marker1->setPrefixJavascriptVariable('marker_');
        $marker1->setPosition(47.214048, -1.585698, true);

        $event1 = $this->get('ivory_google_map.event');
        $event1->setInstance($marker1->getJavascriptVariable());
        $event1->setEventName('click');
        $event1->setHandle('function(){showShopInMap("boucherie-zola")}');

        # le boulanger de zola
        $marker1bis = $this->get('ivory_google_map.marker');
        $marker1bis->setPrefixJavascriptVariable('marker_');
        $marker1bis->setPosition(47.214061, -1.585741, true);
        $markerImage1bis = $this->get('ivory_google_map.marker_image');
        $markerImage1bis->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker1bis->setIcon($markerImage1bis);

        $event1bis = $this->get('ivory_google_map.event');
        $event1bis->setInstance($marker1bis->getJavascriptVariable());
        $event1bis->setEventName('click');
        $event1bis->setHandle('function(){showShopInMap("le-boulanger-de-zola")}');

        # copernic
        $marker2 = $this->get('ivory_google_map.marker');
        $marker2->setPrefixJavascriptVariable('marker_');
        $marker2->setPosition(47.215545,-1.564271, true);

        $event2 = $this->get('ivory_google_map.event');
        $event2->setInstance($marker2->getJavascriptVariable());
        $event2->setEventName('click');
        $event2->setHandle('function(){showShopInMap("boucherie-copernic")}');

        # gourmets
        $marker3 = $this->get('ivory_google_map.marker');
        $marker3->setPrefixJavascriptVariable('marker_');
        $marker3->setPosition(47.229044,-1.57163, true);

        $event3 = $this->get('ivory_google_map.event');
        $event3->setInstance($marker3->getJavascriptVariable());
        $event3->setEventName('click');
        $event3->setHandle('function(){showShopInMap("boucherie-des-gourmets")}');

        # bouffay
        $marker4 = $this->get('ivory_google_map.marker');
        $marker4->setPrefixJavascriptVariable('marker_');
        $marker4->setPosition(47.214962,-1.55429, true);

        $event4 = $this->get('ivory_google_map.event');
        $event4->setInstance($marker4->getJavascriptVariable());
        $event4->setEventName('click');
        $event4->setHandle('function(){showShopInMap("boucherie-du-bouffay")}');

        # Epi de blais
        $marker5 = $this->get('ivory_google_map.marker');
        $marker5->setPrefixJavascriptVariable('marker_');
        $marker5->setPosition(46.594289,0.36257, true);

        $event5 = $this->get('ivory_google_map.event');
        $event5->setInstance($marker5->getJavascriptVariable());
        $event5->setEventName('click');
        $event5->setHandle('function(){showShopInMap("banette-buxerolles")}');
        $markerImage5 = $this->get('ivory_google_map.marker_image');
        $markerImage5->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker5->setIcon($markerImage5);

        # La garenne
        $marker6 = $this->get('ivory_google_map.marker');
        $marker6->setPrefixJavascriptVariable('marker_');
        $marker6->setPosition(46.556027,0.304871, true);
        $markerImage6 = $this->get('ivory_google_map.marker_image');
        $markerImage6->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker6->setIcon($markerImage6);

        $event6 = $this->get('ivory_google_map.event');
        $event6->setInstance($marker6->getJavascriptVariable());
        $event6->setEventName('click');
        $event6->setHandle('function(){showShopInMap("banette-la-garenne")}');

        # Inopinee
        $marker7 = $this->get('ivory_google_map.marker');
        $marker7->setPrefixJavascriptVariable('marker_');
        $marker7->setPosition(46.564791,0.356863, true);
        $markerImage7 = $this->get('ivory_google_map.marker_image');
        $markerImage7->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker7->setIcon($markerImage7);

        $event7 = $this->get('ivory_google_map.event');
        $event7->setInstance($marker7->getJavascriptVariable());
        $event7->setEventName('click');
        $event7->setHandle('function(){showShopInMap("banette-grand-large")}');

        # Les mimines
        $marker8 = $this->get('ivory_google_map.marker');
        $marker8->setPrefixJavascriptVariable('marker_');
        $marker8->setPosition(47.275619,-1.466761, true);
        $markerImage8 = $this->get('ivory_google_map.marker_image');
        $markerImage8->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker8->setIcon($markerImage8);

        $event8 = $this->get('ivory_google_map.event');
        $event8->setInstance($marker8->getJavascriptVariable());
        $event8->setEventName('click');
        $event8->setHandle('function(){showShopInMap("banette-la-mimine")}');

        # Futuroscope
        $marker9 = $this->get('ivory_google_map.marker');
        $marker9->setPrefixJavascriptVariable('marker_');
        $marker9->setPosition(46.660674,0.363318, true);
        $markerImage9 = $this->get('ivory_google_map.marker_image');
        $markerImage9->setUrl('http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png');
        $marker9->setIcon($markerImage8);

        $event9 = $this->get('ivory_google_map.event');
        $event9->setInstance($marker9->getJavascriptVariable());
        $event9->setEventName('click');
        $event9->setHandle('function(){showShopInMap("banette-futuroscope")}');

        $map->addMarker($marker1);
        $map->addMarker($marker1bis);
        $map->addMarker($marker2);
        $map->addMarker($marker3);
        $map->addMarker($marker4);
        $map->addMarker($marker5);
        $map->addMarker($marker6);
        $map->addMarker($marker7);
        $map->addMarker($marker8);
        $map->addMarker($marker9);

        // It can only be used with a DOM event
        // By default, the capture flag is false
        $event1->setCapture(true);
        $event1bis->setCapture(true);
        $event2->setCapture(true);
        $event3->setCapture(true);
        $event4->setCapture(true);
        $event5->setCapture(true);
        $event6->setCapture(true);
        $event7->setCapture(true);
        $event8->setCapture(true);
        $event9->setCapture(true);

        // Add a DOM event
        $map->getEventManager()->addDomEvent($event1);
        $map->getEventManager()->addDomEvent($event1bis);
        $map->getEventManager()->addDomEvent($event2);
        $map->getEventManager()->addDomEvent($event3);
        $map->getEventManager()->addDomEvent($event4);
        $map->getEventManager()->addDomEvent($event5);
        $map->getEventManager()->addDomEvent($event6);
        $map->getEventManager()->addDomEvent($event7);
        $map->getEventManager()->addDomEvent($event8);
        $map->getEventManager()->addDomEvent($event9);

        return array('map' => $map);
    }

    /**
     * @Template()
     * @Route("les-commercants/nantes", name="shops_nantes")
     */
    public function shopsNantesAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Les commerçants à Nantes");

        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);

        $map->setCenter(47.223066, -1.552162, true);
        $map->setMapOption('zoom', 11);

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

        # Les mimines
        $marker8 = $this->get('ivory_google_map.marker');
        $marker8->setPrefixJavascriptVariable('marker_');
        $marker8->setPosition(47.275619,-1.466761, true);

        $map->addMarker($marker1);
        $map->addMarker($marker2);
        $map->addMarker($marker3);
        $map->addMarker($marker4);
        $map->addMarker($marker8);

        return array('map' => $map);
    }

    /**
     * @Template()
     * @Route("les-commercants/poitiers", name="shops_poitiers")
     */
    public function shopsPoitiersAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Les commerçants à Poitiers");

        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);

        $map->setCenter(46.582462, 0.340405, true);
        $map->setMapOption('zoom', 12);

        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);

        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '400px'
        ));

        // Configure your marker options

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

        $map->addMarker($marker5);
        $map->addMarker($marker6);
        $map->addMarker($marker7);

        return array('map' => $map);
    }

    /**
     * @Template()
     * @Route("comment-ca-marche", name="ccm")
     */
    public function ccmAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Comment ça marche");

        return array();
    }

    /**
     * @Template()
     * @Route("mentions-legales", name="legal")
     */
    public function legalAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Mentions légales");

        return array();
    }

    /**
     * @Template()
     * @Route("qui-sommes-nous", name="about")
     */
    public function aboutAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - Qui sommes nous");

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

        $data['menus'] = $em->getRepository('PiggyBoxShopBundle:Menu')->findByShop($shop);
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

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($data['product']->getName()." au commerce ".$shop->getName()." sur Côtelettes & Tarte aux Fraises");

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
        $seoPage = $this->get('sonata.seo.page');

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
            $seoPage->setTitle($data['shop']->getName()." sur Côtelettes & Tarte aux Fraises");

            return $data;
        }

        if ($category_slug == "menus") {
            $seoPage->setTitle("Formules au commerce ".$data['shop']->getName());

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
        $seoPage->setTitle($category->getTitle()." au commerce ".$shop->getName()." sur Côtelettes & Tarte aux Fraises");

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
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle($menu->getTitle()." au commerce ".$menu->getShop()->getName()." sur Côtelettes & Tarte aux Fraises");

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
