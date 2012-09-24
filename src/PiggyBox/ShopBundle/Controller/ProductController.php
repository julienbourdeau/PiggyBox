<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\Sales;
use PiggyBox\ShopBundle\Form\ProductType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Product controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin/mesproduits")
 */
class ProductController extends Controller
{
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

        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
		
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
     * Finds and displays a Product entity.
     *
     * @Route("/{id}/show", name="monmagasin_mesproduits_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Ajouter un nouveau produit 
     *
     * @Route("/new", name="monmagasin_mesproduits_ajouter")
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
             // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();

			// Adding Sales entity relaton to the product
			$sales = new Sales();
			$product->setSales($sales);
			if(false !== $product->getPrices()->first()){
				$product->setMinPrice($product->getPrices()->first()->getPrice());
			}

			// saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
			$product->setShop($user->getOwnshop());
			//inutile à checker $user->getOwnshop()->addProduct($product);
            $em->flush();
		
			// creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($product);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
			$aclProvider->updateAcl($acl);

            return $this->redirect($this->generateUrl('monmagasin_mesproduits_show', array('id' => $product->getId())));
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
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);
        $securityContext = $this->get('security.context');		

        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
		}

		if(!$securityContext->isGranted('VIEW', $product)){
			throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires.');
		}		

        $editForm = $this->createForm(new ProductType(), $product);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $product,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}/update", name="monmagasin_mesproduits_update")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Product:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);
        $securityContext = $this->get('security.context');		

        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

		if(!$securityContext->isGranted('EDIT', $product)){
			throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires.');
		}

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductType(), $product);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('monmagasin_mesproduits_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $product,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}/delete", name="monmagasin_mesproduits_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);
	        $securityContext = $this->get('security.context');		

            if (!$product) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

			if(!$securityContext->isGranted('DELETE', $product)){
				throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires.');
			}
			

            $em->remove($product);
            $em->flush();
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
	 *  Merge the arrays passed to the function and keep the keys intact.
	 *  If two keys overlap then it is the last added key that takes precedence.
	 * 
	 * @return Array the merged array
	 */
	function array_merge_maintain_keys() {
	    $args = func_get_args();
	    $result = array();
	    foreach ( $args as &$array ) {
	        foreach ( $array as $key => &$value ) {
	            $result[$key] = $value;
	        }
	    }
	    return $result;
	}
}
