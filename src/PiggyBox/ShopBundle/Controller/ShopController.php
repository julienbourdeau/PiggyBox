<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\ShopBundle\Form\ShopType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Shop controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin")
 */
class ShopController extends Controller
{
    /**
     * Homepage of the shop-owner. The goal of this page is to 
     *
     * @Route("/", name="moncommerce")
     * @Template()
     */
    public function indexAction()
	{
		NOTE: 
	   		//TODO: 
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PiggyBoxShopBundle:Shop')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Shop entity.
     *
     * @Route("/{id}/show", name="moncommerce_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Shop entity.
     *
	 * @Secure(roles="ROLE_ADMIN")
     * @Route("/new", name="moncommerce_new")
     * @Template()
     */
    public function newAction()
    {
		//NOTE: Méthode permettant de créer un nouveau magasin avec les ACL de l'utilisateur avec le ROLE_ADMIN
		//TODO: Ajouter plus de détails au magasin que le nom et type...
        $shop = new Shop();
        $form = $this->createForm(new ShopType(), $shop);

        return array(
            'entity' => $shop,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Shop entity.
     *
	 * @Secure(roles="ROLE_ADMIN")
     * @Route("/create", name="moncommerce_create")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Shop:new.html.twig")
     */
    public function createAction(Request $request)
    {
		//TODO: Vérifier la validité du formulaire
		//TODO: Si le formulaire est correct, donner les ACL à l'utilisateur connécté
		//TODO: Ajouter le rôle de l'utilisateur ROLE_SHOP
		//TODO: Retirer le ROLE_ADMIN
        $shop = new Shop();
        $form = $this->createForm(new ShopType(), $shop);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
            $em->flush();
		
			// creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($shop);
            $acl = $aclProvider->createAcl($objectIdentity);

            // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
			$aclProvider->updateAcl($acl);


			$manipulator = $this->get('fos_user.util.user_manipulator');
			$manipulator->addRole($user,"ROLE_SHOP");
			$manipulator->removeRole($user,"ROLE_ADMIN");

            return $this->redirect($this->generateUrl('moncommerce_show', array('id' => $shop->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Shop entity.
     *
     * @Route("/{id}/edit", name="moncommerce_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop entity.');
        }

        $editForm = $this->createForm(new ShopType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Shop entity.
     *
     * @Route("/{id}/update", name="moncommerce_update")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Shop:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Shop entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ShopType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('moncommerce_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Shop entity.
     *
     * @Route("/{id}/delete", name="moncommerce_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Shop entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('moncommerce'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
