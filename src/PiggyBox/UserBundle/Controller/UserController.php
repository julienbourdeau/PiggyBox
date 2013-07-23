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
use Ivory\GoogleMap\MapTypeId;
use Geocoder\HttpAdapter\CurlHttpAdapter;
use Geocoder\Geocoder;
use Geocoder\Provider\FreeGeoIpProvider;
use Geocoder\Provider\MaxMindProvider;
use Geocoder\Provider\GoogleMapsProvider;
use Geocoder\Exception\NoResultException;

/**
 * User controller.
 *
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * Affiche la carte sur la homepage
     * 
     * @Template()
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        // Geoloc
        $geoDataVisitor = $this->getGeoDataVisitor();
        $latitude  = $geoDataVisitor['geoResponse']->getLatitude();
        $longitude = $geoDataVisitor['geoResponse']->getLongitude();

        // SEO
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - La commande en ligne pour vos commerces de proximité");

        // Map + Markers
        $map = $this->configureGoogleMap($geoDataVisitor['visitorBigCity']);
        $map = $this->configureMarkers($map);

        // Magasins détails
        $shoppersDetails = $this->getShoppersDetails("all");

        return array(
            'map'              => $map,
            'visitorCity'      => $geoDataVisitor['visitorCity'],
            'visitorBigCity'   => $geoDataVisitor['visitorBigCity'],
            'availableCities'  => $this->getAvailableCities(),
            'shoppersDetails'  => $shoppersDetails,
            'promotedShopper'  => $shoppersDetails['boucherie-des-gourmets'], // premier élément
            'coordinates'      => array('latitude'=>$latitude, 'longitude'=>$longitude),
        );
    }

    /**
     * Gère la page des commerçants les plus proches de l'internaute (après avoir utilisé le input)
     * 
     * @Template()
     * @Route("vos-commerces/{nbShoppers}", name="customShops", defaults={"nbShoppers"=2})
     */
    public function customShopsAction($nbShoppers)
    {
        // Geoloc
        $geoDataVisitor = $this->getGeoDataVisitor();
        $latitude  = $geoDataVisitor['geoResponse']->getLatitude();
        $longitude = $geoDataVisitor['geoResponse']->getLongitude();

        // Get POST (and force it if empty)
        $street_name = $this->get('request')->request->get('street_name',$geoDataVisitor['visitorBigCity']);

        // SEO
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - La commande en ligne pour vos commerces de proximité");

        // Tri les magasins par distance
        try {
            $shoppersDetailsByDistance = $this->getShoppersDetailsByDistance("all",$street_name);
        } catch(NoResultException $e) {
            $street_name = "Nantes";
            $shoppersDetailsByDistance = $this->getShoppersDetailsByDistance("all",$street_name);
        }

        // Récupérer les produits des N premiers commerces.
        $keys = array_keys($shoppersDetailsByDistance);
        $em = $this->getDoctrine()->getManager();
        for ($i=0;$i<$nbShoppers;$i++) {
            // Permet de récupérer la key via l'index
            $slug = $keys[$i];

            $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($slug);
            $products = $em->getRepository('PiggyBoxShopBundle:Product')->findByActiveProduct($shop->getId());
            shuffle($products);

            $shoppersDetailsByDistance[$slug]['products'] = $products;
            $shoppersDetailsByDistance[$slug]['fakeSocialStream'] = $this->getFakeSocialStream($slug);
        }

        // Map + Markers
        // La bigCity du visiteur est celle des commerces les plus proches
        // On force la bigCity ici au cas où le mec ait tapé une adresse autre que celle de SA ville.
        $visitorBigCity = ucfirst(reset($shoppersDetailsByDistance)['bigCity']);
        $map = $this->configureGoogleMap($visitorBigCity);
        $map = $this->configureMarkers($map);

        return array(
            'map'              => $map,
            'street_name'      => $street_name,
            'visitorCity'      => $geoDataVisitor['visitorCity'],
            'visitorBigCity'   => $visitorBigCity,
            'shoppersDetails'  => $shoppersDetailsByDistance,
            'promotedShopper'  => $shoppersDetailsByDistance['boucherie-des-gourmets'], // premier élément
            'nbShoppers'       => $nbShoppers, // Nb de shoppers à indiquer "proche de chez vous"
            'coordinates'      => array('latitude'=>$latitude, 'longitude'=>$longitude),
        );
    }

    /**
     * Page qui liste les commerçants _PAR VILLE_, joli listing + carte de la ville
     * 
     * @Template()
     * @Route("les-commercants/{city}", name="shops", defaults={"city"="geoip"})
     */
    public function shopsAction($city)
    {
        // Formatage de la city
        $city = ucfirst(strtolower($city));

        // Si le mec s'amuse avec l'URL et met une ville inexistante, on force à "geoip"
        if(!(array_key_exists($city, $this->getAvailableCities())) && $city != "geoip") {
            $city = "geoip";
        }

        // Geoloc (forcé avec $city si tout va bien)
        $geoDataVisitor = $this->getGeoDataVisitor($city);
        $latitude  = $geoDataVisitor['geoResponse']->getLatitude();
        $longitude = $geoDataVisitor['geoResponse']->getLongitude();

        // SEO
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises - La commande en ligne pour vos commerces de proximité");

        // Map + Markers
        if($city == "geoip") {
            $map = $this->configureGoogleMap($geoDataVisitor['visitorBigCity']);
        }
        else {
            $map = $this->configureGoogleMap($city);
        }
        $map = $this->configureMarkers($map);

        // Magasins détails
        $shoppersDetails = $this->getShoppersDetails("all");

        return array(
            'map'              => $map,
            'visitorCity'      => $geoDataVisitor['visitorCity'],
            'visitorBigCity'   => $geoDataVisitor['visitorBigCity'],
            'availableCities'  => $this->getAvailableCities(),
            'shoppersDetails'  => $shoppersDetails,
            'promotedShopper'  => $shoppersDetails['boucherie-des-gourmets'],
            'coordinates'      => array('latitude'=>$latitude, 'longitude'=>$longitude),
        );
    }

    /**
     * @Template()
     * @Route("comment-ca-marche", name="ccm")
     */
    public function ccmAction()
    {
        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Commander en ligne dans ses commerces de proximité - Côtelettes & Tarte aux Fraises");

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
     * @Route("commerce/{shop_slug}/produits/{category_slug}/{product_slug}", name="view_product_details")
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

        $shoppers = $this->getShoppersDetails("all");
        $slug = $shop->getSlug();
        $data['shopper'] = $shoppers[$slug];

        return $data;
    }

    /**
     * Récupère les produits d'un magasin selon la catégorie
     *
     * @Route("commerce/{shop_slug}/produits/{category_slug}", name="user_show_shop", defaults={"category_slug"="default"})
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

        $shoppers = $this->getShoppersDetails("all");
        $slug = $shop->getSlug();
        $data['shopper'] = $shoppers[$slug];

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

        $shoppers = $this->getShoppersDetails("all");
        $slug = $shop->getSlug();
        $data['shopper'] = $shoppers[$slug];

        return $data;
    }

    /**
     * @Route("commerce/{shop_slug}/informations-et-details", name="user_show_shop_info")
     * @Template()
     */
    public function showShopInfoAction($shop_slug)
    {

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises");

        $em = $this->getDoctrine()->getManager();
        $data['shop'] = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($shop_slug);

        $shoppers = $this->getShoppersDetails("all");
        $data['shopper'] = $shoppers[$shop_slug];

        return $data;
    }

    /**
     * @Route("commerce/{shop_slug}/horaires-ouvertures", name="user_show_shop_opening_time")
     * @Template()
     */
    public function showShopOpeningTimeAction($shop_slug)
    {

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises");

        $em = $this->getDoctrine()->getManager();
        $data['shop'] = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($shop_slug);

        $shoppers = $this->getShoppersDetails("all");
        $data['shopper'] = $shoppers[$shop_slug];

        return $data;
    }

    /**
     * @Route("commerce/{shop_slug}/photos", name="user_show_shop_photo")
     * @Template()
     */
    public function showShopPhotosAction($shop_slug)
    {

        $seoPage = $this->get('sonata.seo.page');
        $seoPage->setTitle("Côtelettes & Tarte aux Fraises");

        $em = $this->getDoctrine()->getManager();
        $data['shop'] = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($shop_slug);

        $shoppers = $this->getShoppersDetails("all");
        $data['shopper'] = $shoppers[$shop_slug];

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


    /**
     * Retourne une Google Map bien configurée
     * @param  [string] $city La ville sur laquelle centrer la map
     * @return [object] Une Google Map configurée
     */
    private function configureGoogleMap($city)
    {
        $availableCities = $this->getAvailableCities();

        $map = $this->get('ivory_google_map.map');
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setAsync(false);
        $map->setMapOption('scrollwheel', false);
        $map->setMapOption('disableDefaultUI', true);
        $map->setMapOption('zoomControl', true);
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('zoom', 12);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '500px'
        ));

        // On centre sur la ville si l'utilisateur est localisé
        if ($city != "geoip") {
            $map->setCenter($availableCities[$city]['lat'], $availableCities[$city]['long'], true);
        }

        return $map;
    }

    /**
     * Ajoute les markers sur $map
     * @param [object] $map La carte bien configurée
     */
    private function configureMarkers($map)
    {

        $availableCities = $this->getAvailableCities();
        foreach ($availableCities as $city => $coordinates) {

            $shoppers = $this->getShoppersDetails($city);

            // Génération des markers
            foreach ($shoppers as $shopper) {
                $marker[$shopper['slug']] = $this->get('ivory_google_map.marker');
                $marker[$shopper['slug']]->setPrefixJavascriptVariable('marker_');
                $marker[$shopper['slug']]->setPosition($shopper['coordinates'][0], $shopper['coordinates'][1], true);
                $marker[$shopper['slug']]->setIcon($shopper['coordinates'][2]);

                $event[$shopper['slug']] = $this->get('ivory_google_map.event');
                $event[$shopper['slug']]->setInstance($marker[$shopper['slug']]->getJavascriptVariable());
                $event[$shopper['slug']]->setEventName('mouseover');
                $event[$shopper['slug']]->setHandle('function(){showShopInMap("'.$shopper['slug'].'");}');

                $map->addMarker($marker[$shopper['slug']]);
                $event[$shopper['slug']]->setCapture(true);
                $map->getEventManager()->addDomEvent($event[$shopper['slug']]);
            }
        }

        return $map;
    }

    /**
     * Récupère des info sur la position du visiteur
     * @param  string Si $city != "geoip", on force la geoloc sur $city, comme si l'user y était.
     * @return [array]   visitorCity, bigCity, geoResponse
     */
    private function getGeoDataVisitor($city="geoip")
    {
        // Geocoder
        $request  = Request::createFromGlobals();

        $adapter  = new CurlHttpAdapter();
        $geocoder = new Geocoder();

        $geocoder->registerProviders(array(
                                    new FreeGeoIpProvider($adapter), // gratos mais sucks
                                    new MaxMindProvider($adapter, "7u3qk0raLxe0", 'f'),
                                    new GoogleMapsProvider($adapter),
                                    ));

        // Géolocalise via l'IP si aucune ville n'est forcée
        if ($city == "geoip") {
            if (in_array($this->get('kernel')->getEnvironment(), array('dev', 'test'))) {
                $georesponse = $geocoder
                    ->using('free_geo_ip')
                    ->geocode('82.231.144.71'); // Nantes
                    // ->geocode('173.194.40.183'); // Mountain View
            }
            if (!in_array($this->get('kernel')->getEnvironment(), array('dev', 'test'))) {
                $georesponse = $geocoder
                    ->using('maxmind')
                    ->geocode($request->getClientIp());
            }
        } else {
            $georesponse = $geocoder
                            ->using('google_maps')
                            ->geocode($city." France"); // Force city
        }

        // Si on n'a pas trouvé de ville avec la geoloc, on en force une.
        if ($georesponse->getCity()) {
            $visitorCity = $georesponse->getCity();
        } else {
            $visitorCity = "Paris";
        }

        // Cherche la "bigCity" (ville où CETAF a des commerces) aux alentours du visiteur.
        // S'il est dans un rayon de $perimeterKms, alors on lui dit qu'il appartient à la bigCity.
        // Sinon, on lui dit que CETAF n'est pas dispo chez lui.
        // Au final, $bigCity contient la ville proche du visiteur avec des commerces.
        // S'il n'y en a aucune, $bigCity contient "geoip".

        $bigCity           =  "geoip";                       // La grosse ville proche du visiteur
        $perimeterKms      =  100;                          // Le perimètre autour d'une big city
        $availableCities   =  $this->getAvailableCities();
        $directions        =  $this->get('ivory_google_map.directions');

        foreach ($availableCities as $city => $coordinate) {
            // Direction entre la ville du visiteur et une bigCity
            $direcResponse = $directions->route($visitorCity." France", $city." France");

            // Le "[0]" vient de "la route 0", car gMaps en propose toujours 2 ou 3. On prend la meilleure.
            // Si ça n'existe pas (pas de route dispo, plutôt rare), on quitte.
            if (!isset($direcResponse->getRoutes()[0])) {
                break;
            }

            $distance = $direcResponse->getRoutes()[0]->getLegs()[0]->getDistance()->getValue();
            if ($distance <= $perimeterKms*1000) {
                $bigCity = $city;
                break;
            }
        }

        return array(
            'visitorCity'       => $visitorCity,        // La ville exacte du visiteur
            'visitorBigCity'    => $bigCity,            // La ville dispo la plus proche (ou "geoip")
            'geoResponse'       => $georesponse,        // Raw object response
        );
    }

    /**
     * Retourne les informations sur les magasins de $city
     * /!\ TRÈS IMPORTANT : TOUTES LES BOUTIQUES DOIVENT AVOIR LES MÊMES CHAMPS DANS L'ARRAY.
     *     (Sinon, la fonction de tri par distance ne fonctionnera plus)
     * @param  string $city La ville dont on veut les magasins (ou "all" pour toutes)
     * @return array  Informations sur les magasins.
     */
    private function getShoppersDetails($city)
    {
        $city = strtolower($city);
        $content = array();

        // Gestion des markers
        // Les icones sont volés ici : http://www.shutterstock.com/pic.mhtml?id=115204399
        // Marker PSD ici : http://www.premiumpixels.com/freebies/map-location-pins-psd/
        $markerIconBread    = "http://www.cotelettes-tarteauxfraises.com/bundles/piggyboxuser/img/icons/markerIconBread.png";
        $markerIconMeat     = "http://www.cotelettes-tarteauxfraises.com/bundles/piggyboxuser/img/icons/markerIconMeat.png";
        $markerIconStrawberry     = "http://www.cotelettes-tarteauxfraises.com/bundles/piggyboxuser/img/icons/markerIconStrawberry.png";

        if ($city == "nantes" || $city == "all") {

            $content['boucherie-zola']  = array(
                    'slug'          => "boucherie-zola",
                    'name'          => "Boucherie de Zola",
                    'slogan'        => "Venez déguster leurs délicieuses lasagnes.",
                    'description'   => "Stéphane et Myriam Bourdeau ont le plaisir de vous accueillir à Zola. Profitez d'un espace convivial au coeur d'une place dynamique et d'un grand parking gratuit.",
                    'address'       => "6 place Emile Zola 44100 Nantes",
                    'comingSoon'    => false,
                    'coordinates'   => array(47.214048,-1.585698,$markerIconMeat),
                    'telephone'     => "02 40 46 50 42",
                    'email'         => "zola@boucherdefrance.fr",
                    'bigCity'       => "nantes",
                    );
            $content['boucherie-des-gourmets'] = array(
                    'slug'          => "boucherie-des-gourmets",
                    'name'          => "Boucherie des Gourmets",
                    'slogan'        => "Boucherie traditionnelle aux Hauts Pavés",
                    'description'   => "Marie-Noëlle & Bruno vous accueillent au rond point de Vannes depuis 1996 dans une boutique chaleureuse.",
                    'comingSoon'    => false,
                    'address'       => "136 rue des hauts pavés 44000 Nantes",
                    'coordinates'   => array(47.229044,-1.57163,$markerIconMeat),
                    'telephone'     => "02 72 89 08 53",
                    'email'         => "boucherie.jauneau@hotmail.fr",
                    'bigCity'       => "nantes",
                    );
            $content['boucherie-copernic'] = array(
                    'slug'          => "boucherie-copernic",
                    'name'          => "Boucherie Copernic",
                    'slogan'        => "Chez mon Boucher rue Copernic",
                    'description'   => "Jérome et Nadine Hamard ainsi que leurs deux employés vous accueillent dans leur boutique ambiance “boucherie Parisienne”.",
                    'comingSoon'    => false,
                    'address'       => "1 Rue Copernic 44000 Nantes",
                    'coordinates'   => array(47.215545,-1.564271,$markerIconMeat),
                    'telephone'     => "02 40 40 85 68",
                    'email'         => "boucherie.copernic@orange.fr",
                    'bigCity'       => "nantes",
                    );
            $content['le-boulanger-de-zola'] = array(
                    'slug'          => "le-boulanger-de-zola",
                    'name'          => "Le Boulanger de Zola",
                    'slogan'        => "Du pain naturel et bon",
                    'description'   => "Eric et Séverine vous proposent des pains sains à base de farines naturelles. Venez découvrir leurs pains originaux.",
                    'comingSoon'    => false,
                    'address'       => "6 place Emile Zola 44100 Nantes",
                    'coordinates'   => array(47.214061, -1.585741,$markerIconBread),
                    'telephone'     => "02 40 46 44 39",
                    'email'         => "leboulangerdezola@free.fr",
                    'bigCity'       => "nantes",
                    );
            $content['boucherie-morel'] = array(
                    'slug'          => "boucherie-morel",
                    'name'          => "La Boucherie Morel",
                    'slogan'        => "Boucherie Morel au coeur de Rezé",
                    'description'   => "Après plus de 10 ans de métier, Lionel vous propose un large choix de produits en viande, volaille, traiteur et fromage.",
                    'comingSoon'    => false,
                    'address'       => "6 Rue Aristide Briand, 44400 Rezé",
                    'coordinates'   => array(47.1849,-1.546154,$markerIconMeat),
                    'telephone'     => "02 40 75 65 73",
                    'email'         => "morel.lionel@boucherdefrance.fr",
                    'bigCity'       => "nantes",
                    );

        }

        if ($city == "poitiers" || $city == "all") {

            $content['banette-futuroscope'] = array(
                    'slug'          => "banette-futuroscope",
                    'name'          => "Banette Futuroscope",
                    'slogan'        => "Bientôt disponible pour la commande en ligne",
                    'description'   => "",
                    'address'       => "Rue beauregard, 44000 Nantes",
                    'comingSoon'    => true,
                    'coordinates'   => array(46.660674,0.363318,$markerIconBread),
                    'telephone'     => "02 40 46 50 42",
                    'email'         => "zola@boucherdefrance.fr",
                    'bigCity'       => "poitiers",
                    );
            $content['banette-buxerolles'] = array(
                    'slug'          => "banette-buxerolles",
                    'name'          => "Banette Buxerolles",
                    'slogan'        => "Bientôt disponible pour la commande en ligne",
                    'description'   => "",
                    'address'       => "Rue beauregard, 44000 Nantes",
                    'comingSoon'    => true,
                    'coordinates'   => array(46.594289,0.36257,$markerIconBread),
                    'telephone'     => "02 40 46 50 42",
                    'email'         => "zola@boucherdefrance.fr",
                    'bigCity'       => "poitiers",
                    );
            $content['banette-la-garenne'] = array(
                    'slug'          => "banette-la-garenne",
                    'name'          => "Banette La Garenne",
                    'slogan'        => "Bientôt disponible pour la commande en ligne",
                    'description'   => "",
                    'address'       => "Rue beauregard, 44000 Nantes",
                    'comingSoon'    => true,
                    'coordinates'   => array(46.556027,0.304871,$markerIconBread),
                    'telephone'     => "02 40 46 50 42",
                    'email'         => "zola@boucherdefrance.fr",
                    'bigCity'       => "poitiers",
                    );
            $content['banette-grand-large'] = array(
                    'slug'          => "banette-grand-large",
                    'name'          => "Banette Grand Large",
                    'slogan'        => "Bientôt disponible pour la commande en ligne",
                    'description'   => "",
                    'address'       => "Rue beauregard, 44000 Nantes",
                    'comingSoon'    => true,
                    'coordinates'   => array(46.564791,0.356863,$markerIconBread),
                    'telephone'     => "02 40 46 50 42",
                    'email'         => "zola@boucherdefrance.fr",
                    'bigCity'       => "poitiers",
                    );

        }

        if ($city == "la-baule" || $city == "all") {

            $content['burban-production'] = array(
                    'slug'          => "burban-production",
                    'name'          => "Burban Production",
                    'slogan'        => "Des fraises cultivées à La Baule depuis 3 générations",
                    'description'   => "",
                    'address'       => "Place du Marché , 44500 La Baule-Escoublac",
                    'comingSoon'    => false,
                    'coordinates'   => array(47.28505,-2.39515,$markerIconStrawberry),
                    'telephone'     => "02 40 61 10 67",
                    'email'         => "contact@burbanproduction.fr",
                    'bigCity'       => "la-baule",
                    );
            $content['grenier-a-pain'] = array(
                    'slug'          => "grenier-a-pain",
                    'name'          => "Grenier à Pain",
                    'slogan'        => "Une autre boulangerie",
                    'description'   => "",
                    'address'       => "",
                    'comingSoon'    => false,
                    'coordinates'   => array(47.28505,-2.39515,$markerIconBread),
                    'telephone'     => "",
                    'email'         => "",
                    'bigCity'       => "la-baule",
                    );
            $content['fleurs-de-toscane'] = array(
                    'slug'          => "fleurs-de-toscane",
                    'name'          => "Fleurs de Toscane",
                    'slogan'        => "slogan",
                    'description'   => "",
                    'address'       => "",
                    'comingSoon'    => false,
                    'coordinates'   => array(47.28505,-2.39515,$markerIconStrawberry),
                    'telephone'     => "",
                    'email'         => "",
                    'bigCity'       => "la-baule",
                    );
            $content['fondant-baulois'] = array(
                    'slug'          => "fondant-baulois",
                    'name'          => "Le Fondant Baulois",
                    'slogan'        => "slogan",
                    'description'   => "",
                    'address'       => "",
                    'comingSoon'    => false,
                    'coordinates'   => array(47.28505,-2.39515,$markerIconStrawberry),
                    'telephone'     => "",
                    'email'         => "infos@lefondantbaulois.com",
                    'bigCity'       => "la-baule",
                    );
        }


        return $content;
    }

    /**
     * Retourne les listes dispo sur CETAF avec leurs coordonnées latitude,longitude
     * @return [array] (ville => array(), ville => array())
     */
    private function getAvailableCities()
    {
        return array(
                "Nantes"    => array('lat' => 47.211406, 'long' => -1.569672),
                "Poitiers"  => array('lat' => 46.58022, 'long' => 0.34037),
                );
    }

    /**
     * Trie les commerçants dans l'ordre décroissants de distance (+ proche au - proche)
     * @param  $bigCity  La bigCity du visiteur
     * @param  $address  L'adresse de départ
     * @return Un array avec les slugs des commerçants
     */
    private function getShoppersDetailsByDistance($bigCity, $address)
    {
        $adapter  = new CurlHttpAdapter();
        $geocoder = new Geocoder();

        $geocoder->registerProviders(array(new GoogleMapsProvider($adapter)));
        $georesponse = $geocoder->using('google_maps')->geocode($address);

        $latitudeStart = $georesponse->getLatitude();
        $longitudeStart = $georesponse->getLongitude();

        // Récupère la distance entre l'utilisateur et chaque commerce de $bigCity
        $shoppersDetails = $this->getShoppersDetails($bigCity);
        foreach ($shoppersDetails as $index => $shopper) {
            $latitudeEnd = $shopper['coordinates'][0];
            $longitudeEnd = $shopper['coordinates'][1];
            // La distance en mètre, brute.
            $shoppersDetails[$index]['distanceFromMe'] = $this->getDistanceBetweenPoints($latitudeStart, $longitudeStart,
                                                                         $latitudeEnd, $longitudeEnd)['meters'];

            // Une belle distance, pour l'affichage.
            $shoppersDetails[$index]['niceDistanceFromMe'] = $this->getNiceDistance($shoppersDetails[$index]['distanceFromMe']);
        }

        // Requête de sort à préparer
        $multisortFunction = 'array_multisort($distanceFromMe, SORT_ASC,';

        // Extrait chaque colonne du tableau dans un tableau à part
        // Obligatoire pour array_multisort
        // Prend les clefs du premier élément, pareilles pour les autres
        $array_keys = array_keys(reset($shoppersDetails));
        foreach ($array_keys as $key) {
            $$key = array_map(create_function('$arr', 'return $arr["'.$key.'"];'), $shoppersDetails);

            // On ajoute "distanceFromMe" à la main plus haut pour qu'il soit premier
            // car c'est cette valeur qui est prioritaire au niveau du tri
            if ($key != 'distanceFromMe') {
                $multisortFunction .= "$$key,";
            }
        }

        // Trie le tableau en utilisant la "requête" générée dans la string
        $multisortFunction = rtrim($multisortFunction, ',');
        $multisortFunction .= ');';
        eval($multisortFunction);

        // Réassemble les tableaux de colonne en tableau de lignes
        $result = array();
        foreach ($array_keys as $key) {
            $content = $$key;
            foreach ($content as $index => $data) {
                $result[$index][$key] = $data;
            }
        }

        return $result;
    }

    /**
     * Calcule la distance entre 2 points (vol d'oiseau)
     * @param  [type] $latitude1
     * @param  [type] $longitude1
     * @param  [type] $latitude2
     * @param  [type] $longitude2
     * @return [type] Un array avec la valeur en plusieurs unités
     */
    private function getDistanceBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers*1000;

        return compact('miles','feet','yards','kilometers','meters');
    }

    /**
     * Retourne une belle distance ("35km", "700m"...)
     * @param  [type] $distance La distance en mètre
     * @return [type] La jolie string
     */
    private function getNiceDistance($distance)
    {
        if ($distance < 1000) {
            return round($distance)." mètres";
        } else {
          return round($distance/1000,2)." kms";
        }
    }

    /**
     * Lit le fichier de la boutique et récupère les info à afficher
     * Le contenu du fichier est du plus récent au plus ancien
     * @return [type] [description]
     */
    private function getFakeSocialStream($slug)
    {
        $periodUpdateInHour = 1; // Update au MAX une fois toutes XXX heures
        $slugFile = __DIR__."/../Resources/public/socialstream/$slug.txt";
        $handle = fopen($slugFile, 'a+');
        $lastStreamUpdate = filemtime($slugFile);

        // Si le fichier de stream n'a pas été updaté depuis Xh, on le remplit
        if ($lastStreamUpdate < time()-($periodUpdateInHour*60*60)) {
            // Génère une ligne avec un prénom ou "inconnu"
            srand((double) microtime()*1000000);
            if (rand(0,4)%2 == 0) {
                $tablePrenoms = file(__DIR__."/../Resources/public/socialstream/prenoms.txt");
                $randomPrenom = ucfirst(rtrim($tablePrenoms[rand(0,count($tablePrenoms))]));
            } else {
                $randomPrenom = "Inconnu";
            }

            // Génère une date comprise entre maintenant et $periodUpdateInHour
            $date = date('Y-m-d H:i:s', time()-(rand(0,60)*60*$periodUpdateInHour));

            // Génère une action
            if (rand(0,4)%5 == 0) {
                $em = $this->getDoctrine()->getManager();
                $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($slug);
                $products = $em->getRepository('PiggyBoxShopBundle:Product')->findByActiveProduct($shop->getId());
                shuffle($products);

                $action = 'Commande';
                $text = ' a commandé '.$products[0]->getName().'.';
            } else {
                $action = 'Visite';
                $text = ' a visité la boutique.';
            }

            // Écrit la ligne
            fputs($handle,$date."|".$action."|".$randomPrenom.$text."\n");
        }

        // Extrait les données du fichier pour l'affichage
        fseek($handle,0);
        $content = array();
        while (($buffer = fgets($handle, 4096)) !== false) {
                $data = explode('|',$buffer);
                if (strtotime($data[0]) <= time()) {
                    $content[] = array('label'=>$data[1], 'text'=>$data[2], 'ago'=>$this->relative_time($data[0]));
                }
        }
        fclose($handle);

        // On prend du plus récent au plus ancien
        $content = array_reverse($content);

        return $content;
    }

    /**
     * Retourne un texte type "il y a 10 secondes"
     * @param  string $date Une date au format string (pas time())
     * @return string
     */
    private function relative_time($date = NULL)
    {
        $diff = time() - strtotime($date);
        if ($diff<60)
            return "il y a ". $diff . " seconde" . $this->plural($diff);
        $diff = round($diff/60);
        if ($diff<60)
            return "il y a ". $diff . " minute" . $this->plural($diff);
        $diff = round($diff/60);
        if ($diff<24)
            return "il y a environ ". $diff . " heure" . $this->plural($diff);
        $diff = round($diff/24);
        if ($diff<7)
            return "il y a ". $diff . " jour" . $this->plural($diff);
        $diff = round($diff/7);
        if ($diff<4)
            return "il y a ". $diff . " semaine" . $this->plural($diff);
        return "le " . date("F j, Y", strtotime($date));
    }

    /**
     * Retourne un S si $num est > 1
     * @param  int    $num
     * @return string 's' ou rien
     */
    private function plural($num = NULL)
    {
        if ($num > 1) return "s";
    }
}
