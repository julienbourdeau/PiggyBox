<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Product;
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
     * Lister tous les produits du magasin
     *
     * @Route("/", name="monmagasin_mesproduits")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PiggyBoxShopBundle:Product')->findAll();

        return array(
            'entities' => $entities,
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
     * @Route("/new", name="monmagasin_mesproduits_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Product();
        $form   = $this->createForm(new ProductType(), $entity);

        return array(
            'entity' => $entity,
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
			
			// saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
			$product->setShop($user->getOwnshop());
			$user->getOwnshop()->addProduct($product);
			var_dump($user->getOwnshop()->getProducts());die();
            $em->flush();
		
			// creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($product);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
			$aclProvider->updateAcl($acl);

			// Ajout du ROLE_SHOP et suppression du ROLE_ADMIN
			$manipulator = $this->get('fos_user.util.user_manipulator');
			$manipulator->addRole($user,"ROLE_SHOP");
			$manipulator->removeRole($user,"ROLE_ADMIN");
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

        $entity = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createForm(new ProductType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
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

        $entity = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('monmagasin_mesproduits_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
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
            $entity = $em->getRepository('PiggyBoxShopBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
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
}
