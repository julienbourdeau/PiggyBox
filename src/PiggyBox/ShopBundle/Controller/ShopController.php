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
use JMS\SecurityExtraBundle\Annotation\SecureReturn;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

/**
 * Shop controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin")
 */
class ShopController extends Controller
{
    /**
     * Homepage of the shop-owner. The goal of this page is receive order and to link all the other page 
	 * 
	 * @SecureReturn(permissions="VIEW")
     * @Route("/", name="moncommerce_mescommandes")
     * @Template()
     */
    public function indexAction()
	{
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PiggyBoxOrderBundle:Order')->findByStatus('toValidate');

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
		//NOTE: Vérification de la validité du formulaire > Ajout du Shop à l'utilisateur > création des ACL à l'objet $user > Ajout du ROLE_SHOP et suppression du ROLE_ADMIN > Redirection vers la route logout
        $shop = new Shop();
        $form = $this->createForm(new ShopType(), $shop);
        $form->bind($request);

        if ($form->isValid()) {
             // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
			
			// saving the DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
			$user->setOwnshop($shop);
            $em->flush();
		
			// creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($shop);
            $acl = $aclProvider->createAcl($objectIdentity);

            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
			$aclProvider->updateAcl($acl);

			// Ajout du ROLE_SHOP et suppression du ROLE_ADMIN
			$manipulator = $this->get('fos_user.util.user_manipulator');
			$manipulator->addRole($user,"ROLE_SHOP");
			$manipulator->removeRole($user,"ROLE_ADMIN");

            return $this->redirect($this->generateUrl('fos_user_security_logout'));
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
		//NOTE: Vérification de l'autorisation 'VIEW' > Présentation du formulaire d'édition du Shop > Création de la vue	
        $em = $this->getDoctrine()->getManager();

        $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);
        $securityContext = $this->get('security.context');		

        if (!$shop) {
            throw $this->createNotFoundException('Unable to find Shop entity.');
        }

		if(!$securityContext->isGranted('VIEW', $shop)){
			throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires.');
		}

        $editForm = $this->createForm(new ShopType(), $shop);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $shop,
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
    public function updateShopAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->find($id);
        $securityContext = $this->get('security.context');		

        if (!$shop) {
            throw $this->createNotFoundException('Unable to find Shop entity.');
        }

		if(!$securityContext->isGranted('EDIT', $shop)){
			throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires.');
		}

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ShopType(), $shop);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($shop);
            $em->flush();

            return $this->redirect($this->generateUrl('moncommerce_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $shop,
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
