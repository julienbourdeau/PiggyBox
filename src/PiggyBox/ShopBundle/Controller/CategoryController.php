<?php

namespace PiggyBox\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PiggyBox\ShopBundle\Entity\Category;
use PiggyBox\ShopBundle\Form\CategoryType;
use PiggyBox\ShopBundle\Form\ChoiceList\CategoryEntityLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Category controller.
 *
 * @PreAuthorize("hasRole('ROLE_ADMIN')")
 * @Route("/category")
 */
class CategoryController extends Controller
{
/**
     * @Route("/", name="demo_category_tree")
     * @Template
     */
    public function treeAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('PiggyBoxShopBundle:Category');

        $self = &$this;
        $options = array(
            'decorate' => true,
            'nodeDecorator' => function($node) use (&$self) {
                $linkEdit = '<a href="' . $self->generateUrl('demo_category_edit', array('id' => $node['id'])) . '">Edit</a>';
                $linkDelete = '<a href="' . $self->generateUrl('demo_category_delete', array('id' => $node['id'])) . '">Delete</a>';
                $linkUp = '<a href="' . $self->generateUrl('demo_category_move_up', array('id' => $node['id'])) . '">Up</a>';
                $linkDown = '<a href="' . $self->generateUrl('demo_category_move_down', array('id' => $node['id'])) . '">Down</a>';
                $linkNode = '<a href="' . $self->generateUrl('demo_category_show', array('slug' => $node['slug']))
                    . '">' . $node['title'] . '</a>'
                ;
                if ($node['level'] !== 0) {
                    $linkNode .= '&nbsp;&nbsp;&nbsp;' . $linkEdit . '&nbsp;' . $linkDelete . '&nbsp;' . $linkUp . '&nbsp;' . $linkDown;
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

        return compact('tree', 'rootNodes');
    }

    /**
     * @Route("/move-up/{id}", name="demo_category_move_up")
     */
    public function moveUpAction($id)
    {
        $node = $this->findNodeOr404($id);
        $repo = $this->get('doctrine.orm.entity_manager')
            ->getRepository('PiggyBoxShopBundle:Category')
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
            ->getRepository('PiggyBoxShopBundle:Category')
        ;

        $repo->moveDown($node);
        return $this->redirect($this->generateUrl('demo_category_tree'));
    }

    /**
     * @Route("/show/{slug}", name="demo_category_show")
     * @Template
     */
    public function showAction($slug)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $dql = <<<____SQL
            SELECT c
            FROM PiggyBoxShopBundle:Category c
            WHERE c.slug = :slug
____SQL;
        $q = $em
            ->createQuery($dql)
            ->setMaxResults(1)
            ->setParameters(compact('slug'))
        ;
        $node = $q->getResult();
        if (!$node) {
            throw $this->createNotFoundException(sprintf(
                'Failed to find Category by slug:[%s]',
                $slug
            ));
        }
        $node = current($node);

        $pathQuery = $em
            ->getRepository('PiggyBoxShopBundle:Category')
            ->getPathQuery($node)
        ;
        $path = $pathQuery->getArrayResult();

        return compact('node', 'path');
    }

    /**
     * @Route("/delete/{id}", name="demo_category_delete")
     */
    public function deleteAction($id)
    {
        $node = $this->findNodeOr404($id);
		$em = $this->get('doctrine.orm.entity_manager');
        $em->remove($node);
        $em->flush();
        $this->get('session')->setFlash('message', 'Category '.$node->getTitle().' was removed');

        return $this->redirect($this->generateUrl('demo_category_tree'));
    }

    /**
     * @Route("/edit/{id}", name="demo_category_edit")
     * @Template
     * @Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $node = $this->findNodeOr404($id);
        $choiceLoader = new CategoryEntityLoader($this, $em, $node);
        $choiseList = new EntityChoiceList(
            $em,
            'PiggyBoxShopBundle:Category',
            'title',
            $choiceLoader
        );
        $form = $this->createForm(new CategoryType($choiseList), $node);
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'), $node);
            if ($form->isValid()) {
                $em->persist($node);
                $em->flush();
                $this->get('session')->setFlash('message', 'Category was updated');
                return $this->redirect($this->generateUrl('demo_category_tree'));
            } else {
                $this->get('session')->setFlash('error', 'Fix the following errors');
            }
        }
        $form = $form->createView();
        return compact('form');
    }

    /**
     * @Route("/add", name="demo_category_add")
     * @Template
     */
    public function addAction()
    {
        $form = $this->createForm(new CategoryType, new Category)->createView();
        return compact('form');
    }

    /**
     * @Route("/save", name="demo_category_save")
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
            return $this->redirect($this->generateUrl('demo_category_tree'));
        } else {
            $this->get('session')->setFlash('error', 'Fix the following errors');
        }
        $form = $form->createView();
        return $this->render('PiggyBoxShopBundle:Category:add.html.twig', compact('form'));
    }

	private function findNodeOr404($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $q = $em->createQuery('SELECT c FROM PiggyBoxShopBundle:Category c WHERE c.id = :id');
        $q->setParameter('id', $id);
        $q->setMaxResults(1);
        $node = $q->getResult();
        if (!$node) {
            throw new NotFoundHttpException(sprintf(
                'Failed to find Category by id:[%s]',
                $id
            ));
        }
        return current($node);
    }
}
