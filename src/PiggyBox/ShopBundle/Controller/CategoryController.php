<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Category;
use PiggyBox\ShopBundle\Form\CategoryType;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category")
     * @Template()
     */
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('PiggyBoxShopBundle:Category');

		$self = &$this;
        $options = array(
            'decorate' => true,
            'nodeDecorator' => function($node) use (&$self) {
                $linkUp = '<a href="' . $self->generateUrl('demo_category_move_up', array('id' => $node['id'])) . '">Up</a>';
                $linkDown = '<a href="' . $self->generateUrl('demo_category_move_down', array('id' => $node['id'])) . '">Down</a>';
                $linkNode = '<a href="' . $self->generateUrl('demo_category_show', array('slug' => $node['slug']))
                    . '">' . $node['title'] . '</a>'
                ;
                if ($node['level'] !== 0) {
                    $linkNode .= '&nbsp;&nbsp;&nbsp;' . $linkUp . '&nbsp;' . $linkDown;
                }
                return $linkNode;
            }
        );
        $query = $em
            ->createQueryBuilder()
            ->select('node')
            ->from('PiggyBoxShopBundle:Category', 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
        ;
        $nodes = $query->getArrayResult();
        $tree = $repo->buildTree($nodes, $options);
        $rootNodes = array_filter($nodes, function ($node) {
            return $node['level'] === 0;
        });

		return compact('tree', 'languages', 'rootNodes');    
	}


    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}/show", name="category_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createForm(new CategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

	/**
     * @Route("/save", name="category_create")
     * @Method("POST")
     */
    public function saveAction()
    {
        $node = new Category;
        $form = $this->createForm(new CategoryType, $node);
        $form->bindRequest($this->get('request'), $node);
        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($node);
            $em->flush();
            $this->get('session')->setFlash('message', 'Category was added');
            return $this->redirect($this->generateUrl('category'));
        } else {
            $this->get('session')->setFlash('error', 'Fix the following errors');
        }
        $form = $form->createView();
        return $this->render('PiggyBoxShopBundle:Category:new.html.twig', compact('form'));
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createForm(new CategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}/update", name="category_update")
     * @Method("POST")
     * @Template("PiggyBoxShopBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PiggyBoxShopBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}/delete", name="category_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('PiggyBoxShopBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

	/**
     * @Route("/move-up/{id}", name="demo_category_move_up")
     */
    public function moveUpAction($id)
    {
        $node = $this->findNodeOr404($id);
        $repo = $this->get('doctrine.orm.entity_manager')
            ->getRepository('Gedmo\DemoBundle\Entity\Category')
        ;

        $repo->moveUp($node);
        return $this->redirect($this->generateUrl('demo_category_tree'));
    }

    /**
     * @Route("/move-down/{id}", name="demo_category_move_down")
     */
    public function moveDownAction($id)
    {
        $node = $this->findNodeOr404($id);
        $repo = $this->get('doctrine.orm.entity_manager')
            ->getRepository('Gedmo\DemoBundle\Entity\Category')
        ;

        $repo->moveDown($node);
        return $this->redirect($this->generateUrl('demo_category_tree'));
    }

    /**
     * @Route("/show/{slug}", name="demo_category_show")
     * @Template
     */
    public function showDemoAction($slug)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = <<<____SQL
            SELECT c
            FROM GedmoDemoBundle:Category c
            WHERE c.slug = :slug
____SQL;
        $q = $em
            ->createQuery($dql)
            ->setMaxResults(1)
            ->setParameters(compact('slug'))
        ;
        $this->setTranslatableHints($q);
        $node = $q->getResult();
        if (!$node) {
            throw $this->createNotFoundException(sprintf(
                'Failed to find Category by slug:[%s]',
                $slug
            ));
        }
        $node = current($node);

        $translationRepo = $em->getRepository(
            'Gedmo:Translation'
        );
        $translations = $translationRepo->findTranslations($node);
        $pathQuery = $em
            ->getRepository('Gedmo\DemoBundle\Entity\Category')
            ->getPathQuery($node)
        ;
        $this->setTranslatableHints($pathQuery);
        $path = $pathQuery->getArrayResult();

        return compact('node', 'translations', 'path');
    }
}
