<?php

namespace PiggyBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\UserBundle\Entity\User;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\OrderBundle\Form\Type\OrderDetailType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Récupère les produits d'un magasin selon la catégorie
     *
     * @Route("commerce/{slug}/{category_title}", name="user_show_shop", defaults={"category_title"="default"})
     * @ParamConverter("shop", options={"mapping": {"slug": "slug"}})
     * @Template()
     */
    public function showShopAction(Request $req, Shop $shop, $category_title)
    {
        if ($category_title == "default") {
            $products = $shop->getProducts();

            return array(
                'shop'      => $shop,
                'products'	  => $products,
                'category_title' => 'tous',
            );
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('PiggyBoxShopBundle:Category')->findOneByTitle($category_title);

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
            'category_title' => $category_title,
        );
    }

    /**
     * @Template()
     * @Route("product/{product_id}.{_format}", name="view_product", requirements={"_format"="(json)"})
     * @ParamConverter("product", class="PiggyBoxShopBundle:Product", options={"id" = "product_id"})
     * @Method({"GET"})
     */
    public function viewProductPriceAction(Request $req, Product $product)
    {
        $order_detail = new OrderDetail();
        $order_detail->setProduct($product);

        $data = array();
        if ($product->getPriceType() == Product::SLICE_PRICE) {
            $i =0;
            foreach ($product->getPrices() as $price) {
                $data['form'][$i] = $this->createForm(new OrderDetailType($product->getPriceType()), $order_detail)->createView();
                $i++;
            }
        }

        if ($product->getPriceType() != Product::SLICE_PRICE) {
            $data['form'] =	$this->createForm(new OrderDetailType($product->getPriceType()), $order_detail)->createView();
        }

        $data['product'] = $product;
        $html = $this->renderView('PiggyBoxUserBundle:User:productDetails.html.twig', $data);

        return new JsonResponse(array('content' => $html));
    }

}
