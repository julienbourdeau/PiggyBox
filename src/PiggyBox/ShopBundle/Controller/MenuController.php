<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Menu;
use PiggyBox\ShopBundle\Entity\MenuItem;
use PiggyBox\ShopBundle\Form\MenuType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Menu controller.
 *
 * @PreAuthorize("hasRole('ROLE_SHOP')")
 * @Route("/monmagasin/formule")
 */
class MenuController extends Controller
{
    /**
     * Lists all Menu entities.
     *
     * @Route("/", name="piggybox_menu_list")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $menus = $em->getRepository('PiggyBoxShopBundle:Menu')->findAll();

        return array(
            'menus' => $menus,
        );
    }

    /**
     * Finds and displays a Menu entity.
     *
     * @Route("/{id}/show", name="piggybox_menu_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Menu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Menu entity.
     *
     * @Route("/nouveau", name="piggybox_menu_new")
     * @Template()
     */
    public function newAction()
    {
        $menu = new Menu();

        $form   = $this->createForm(new MenuType(), $menu);

        return array(
            'entity' => $menu,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Menu entity.
     *
     * @Route("/create", name="piggybox_menu_create")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Menu:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $menu  = new Menu();
        $form = $this->createForm(new MenuType(), $menu);
        $form->bind($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            for ($i = 0; $i < $form->getData()->getStepsNumber(); $i++) {
                $menuItem = new MenuItem();
                $menuItem->setTitle("Etape ".$i);
                $menuItem->setMenu($menu);
                $menu->addMenuItem($menuItem);
            }
            $user = $this->get('security.context')->getToken()->getUser();
            $menu->setShop($user->getOwnshop());

            $em->persist($menu);
            $em->flush();

            return $this->redirect($this->generateUrl('piggybox_menu_show', array('id' => $menu->getId())));
        }

        return array(
            'entity' => $menu,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Menu entity.
     *
     * @Route("/{id}/edit", name="piggybox_menu_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $menu = $em->getRepository('PiggyBoxShopBundle:Menu')->find($id);

        if (!$menu) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $editForm = $this->createForm(new MenuType(), $menu);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'menu'      => $menu,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}/update", name="piggybox_menu_update")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Menu:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Menu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MenuType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('piggybox_menu_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Menu entity.
     *
     * @Route("/{id}/delete", name="piggybox_menu_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PiggyBoxShopBundle:Menu')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Menu entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('piggybox_menu_list'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
