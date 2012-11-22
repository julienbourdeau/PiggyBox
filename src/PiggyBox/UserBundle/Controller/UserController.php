<?php

namespace PiggyBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\UserBundle\Entity\User;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\OrderBundle\Form\Type\OrderDetailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
        return array();
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
     * @Template()
     * @Route("temp", name="temp")
     */
    public function tempAction()
    {
        return array();
    }

    /**
     * @Template()
     * @Route("temp-cart", name="temp_cart")
     */
    public function tempCartAction()
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
        $product = $em->getRepository('PiggyBoxShopBundle:Product')->findOneByShopAndProductSlug($shop->getId(), $product_slug);

        $similarProducts = $em->getRepository('PiggyBoxShopBundle:Product')->findBySimilarProductByShopAndByCategory($shop->getId(), $product->getId(), $product->getCategory());

        $randomProducts = $em->getRepository('PiggyBoxShopBundle:Product')->findByShopExcludeByCategory($shop->getId(), $product->getCategory());

        $orderDetail = new OrderDetail();
        $form = $this->createForm(new OrderDetailType(), $orderDetail);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($shop->getName(), $this->get("router")->generate('user_show_shop', array('slug' => $shop->getSlug())));
        $breadcrumbs->addItem($product->getCategory()->getTitle() , $this->get("router")->generate('user_show_shop', array('slug' => $shop->getSlug(), 'category_title' => $category_slug)));
        $breadcrumbs->addItem($product->getName(), $this->get("router")->generate('view_product_details', array('shop_slug' => $shop->getSlug(), 'category_slug' => $category_slug, 'product_slug' => $product_slug)));

        return array(
            'shop' => $shop,
            'category_title' => $category_slug,
            'product' => $product,
            'similar_products' => $similarProducts,
            'random_products' => $randomProducts,
            'form' => $form,
        );
    }

    /**
     * Récupère les produits d'un magasin selon la catégorie
     *
     * @Route("commerce/{slug}/{category_title}", name="user_show_shop", defaults={"category_title"="default"})
     * @ParamConverter("shop", options={"mapping": {"slug": "slug"}})
     * @Template()
     */
    public function showShopAction(Request $req, Shop $shop, $category_title)
    {
        $orderDetail = new OrderDetail();
        $form = $this->createForm(new OrderDetailType(), $orderDetail);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($shop->getName(), $this->get("router")->generate('user_show_shop', array('slug' => $shop->getSlug())));

        if ($category_title == "default") {
            $products = $shop->getProducts();

            return array(
                'shop'      => $shop,
                'products'	  => $products,
                'form'	  => $form,
                'category_title' => 'tous',
            );
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('PiggyBoxShopBundle:Category')->findOneByTitle($category_title);
        $breadcrumbs->addItem($category->getTitle() , $this->get("router")->generate('user_show_shop', array('slug' => $shop->getSlug(), 'category_title' => $category_title)));

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

        return array(
            'shop'      => $shop,
            'products'	  => $products,
            'form'	  => $form,
            'category_title' => $category_title,
        );
    }
}
