<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\Discount;
use PiggyBox\ShopBundle\Form\DiscountType;
use PiggyBox\ShopBundle\Entity\Category;
use PiggyBox\ShopBundle\Form\ProductType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Product controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin/mesproduits")
 */
class ProductController extends Controller
{
    /** @DI\Inject */
    private $request;

    /** @DI\Inject */
    private $router;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $em;

    /**
     * Lister les produits du magasin, les linker vers le CRUD
     *
     * @Route("/{category_id}", name="monmagasin_mesproduits", requirements={"category_id"="\d+"}, defaults={"category_id"="0"})
     * @Template()
     */
    public function indexAction($category_id)
    {
        //NOTE: Get the list of product by category for the shopowner > link them to the CRUD
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();

        $categories = $query = $em->createQuery('SELECT DISTINCT c, p FROM PiggyBoxShopBundle:Category c JOIN c.products p  WHERE p.shop=:id')
                                  ->setParameter('id', $user->getOwnShop()->getId())
                                  ->getResult();

        //NOTE: Si on affiche les produits faisant parties d'une catégorie
        if (!$category_id) {
            $products = $user->getOwnshop()->getProducts();

            return array(
                'products' => $products,
                'categories' => $categories,
                'flag' => $category_id,
            );
        }

        $repo = $em->getRepository('PiggyBoxShopBundle:Category');
        $category = $repo->find($category_id);

        $children = $repo->children($category);
        $products = $category->getProducts();

        //NOTE: Si il a des enfant
        if ($repo->childCount($category) != 0) {
            //NOTE: Si la catégorie existe dans le magasin
            foreach ($children as $children_category) {
                if (in_array($children_category,$categories)) {
                    $children_category_products = $children_category->getProducts();
                    foreach ($children_category_products as $product_tab) {
                        $products->add($product_tab);
                    }
                }
            }
        }

        return array(
            'products' => $products,
            'categories' => $categories,
            'flag' => $category_id,
        );
    }

    /**
     * Ajouter un nouveau produit
     *
     * @Route("/nouveau", name="monmagasin_mesproduits_ajouter")
     * @Template()
     */
    public function newAction()
    {
        $product= new Product();
        $form   = $this->createForm(new ProductType(), $product);

        return array(
            'entity' => $product,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/create", name="monmagasin_mesproduits_create")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Product:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product);
        $form->bind($request);

        if ($form->isValid()) {
            try {
                 // retrieving the security identity of the currently logged-in user
                $user = $this->get('security.context')->getToken()->getUser();
                $em = $this->getDoctrine()->getManager();

                $em->persist($product);
                $product->setShop($user->getOwnshop());
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', 'Le produit '.$product->getName().' a été ajouté avec succès.');

                return $this->redirect($this->generateUrl('monmagasin_mesproduits'));
            } catch (\Exception $e) {
                $this->get('logger')->crit($e->getMessage(), array('exception', $e));
                $this->get('session')->getFlashBag()->set('error', 'Une erreur est survenue, notre équipe a été prévenue');
            }
        }

        return array(
            'entity' => $product,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="monmagasin_mesproduits_edit")
     * @SecureParam(name="product", permissions="VIEW, EDIT")
     * @ParamConverter
     * @Template()
     */
    public function editAction(Product $product)
    {
        $editForm = $this->createForm(new ProductType(), $product);

        $deleteForm = $this->createDeleteForm($product->getId());

        return array(
            'product'     => $product,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Display a form to edit or create a Discount Entity
     *
     * @Route("/{id}/discount", name="monmagasin_mesproduits_promotion_edit")
     * @SecureParam(name="product", permissions="VIEW, EDIT")
     * @ParamConverter
     * @Template()
     */
    public function editDiscountAction(Product $product)
    {
        if ($product->getDiscount() == null) {
            $discount = new Discount();
            $product->setDiscount($discount);
        }
        if ($product->getDiscount() != null) {
            $discount = $product->getDiscount();
        }

        $editForm = $this->createForm(new DiscountType(), $discount);
        $deleteForm = $this->createDeleteForm($discount->getId());

        if ('POST' === $this->request->getMethod()) {
            $editForm->bindRequest($this->request);

            if ($editForm->isValid()) {

                $this->em->persist($product);
                $this->em->persist($discount);
                $this->em->flush();

                $this->get('session')->getFlashBag()->set('success', 'La promotion a été ajouté avec succès.');

                return new RedirectResponse($this->router->generate('monmagasin_mesproduits'));
            }
        }

        return array(
            'product'     => $product,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Discount entity
     *
     * @Route("/{id}/{productId}/discount/delete", name="monmagasin_mesproduits_discount_delete")
     * @ParamConverter
     * @Method("POST")
     */
    public function deleteDiscountAction(Request $request, Discount $discount, $productId)
    {
        $form = $this->createDeleteForm($discount->getId());
        $form->bind($request);
        $product = $this->em->getRepository('PiggyBoxShopBundle:Product')->findOneById($productId);

        if ($form->isValid()) {
            try {
                $product->setDiscount(null);
                $this->em->persist($product);
                $this->em->remove($discount);
                $this->em->flush();
                $this->get('session')->getFlashBag()->set('success', 'La promotion '.$discount->getDiscountName().' a été supprimé avec succès.');
            } catch (\Exception $e) {
                var_dump($e->getMessage());die();
                $this->get('logger')->crit($e->getMessage(), array('exception', $e));
                $this->get('session')->getFlashBag()->set('error', 'Une erreur est survenue, notre équipe a été prévenue');
            }
        }

        return new RedirectResponse($this->router->generate('monmagasin_mesproduits'));
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}/update", name="monmagasin_mesproduits_update")
     * @SecureParam(name="product", permissions="VIEW, EDIT")
     * @ParamConverter
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Product:edit.html.twig")
     */
    public function updateAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product->getId());
        $editForm = $this->createForm(new ProductType(), $product);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($product);
                $em->flush();

                $this->get('session')->getFlashBag()->set('success', 'Le produit '.$product->getName().' a été édité avec succès.');

                return $this->redirect($this->generateUrl('monmagasin_mesproduits'));
            } catch (\Exception $e) {
                $this->get('logger')->crit($e->getMessage(), array('exception', $e));
                $this->get('session')->getFlashBag()->set('error', 'Une erreur est survenue, notre équipe a été prévenue');
            }

        }

        return array(
            'product'      => $product,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}/delete", name="monmagasin_mesproduits_delete")
     * @SecureParam(name="product", permissions="VIEW, DELETE")
     * @ParamConverter
     * @Method("POST")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product->getId());
        $form->bind($request);

        if ($form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($product);
                $em->flush();
                $this->get('session')->getFlashBag()->set('success', 'Le produit '.$product->getName().' a été supprimé avec succès.');

            } catch (\Exception $e) {
                $this->get('logger')->crit($e->getMessage(), array('exception', $e));
                $this->get('session')->getFlashBag()->set('error', 'Une erreur est survenue, notre équipe a été prévenue');
            }
        }

        return $this->redirect($this->generateUrl('monmagasin_mesproduits'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Change active/inactive status
     *
     * @Route("/{id}/activate", name="monmagasin_mesproduits_activate")
     * @SecureParam(name="product", permissions="VIEW, DELETE")
     * @ParamConverter
     */
    public function setActiveAction(Product $product)
    {
        $em = $this->getDoctrine()->getManager();

        if ($product->getActive() == true) {
              $product->setActive(false);
          } else {
              $product->setActive(true);
          }

    $em->persist($product);
    $em->flush();

    return $this->redirect($this->generateUrl('monmagasin_mesproduits'));
    }
}
