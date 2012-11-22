<?php

namespace PiggyBox\ShopBundle\Form\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class CategoryEntityLoader implements EntityLoaderInterface
{
    private $em;
    private $categoryController;
    private $basedOnNode;

    public function __construct(Controller $c, EntityManager $em, $node = null)
    {
        $this->em = $em;
        $this->categoryController = $c;
        $this->basedOnNode = $node;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntities()
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('c')
            ->from('PiggyBoxShopBundle:Category', 'c')
        ;
        if (!is_null($this->basedOnNode)) {
            $qb->where($qb->expr()->notIn(
                'c.id',
                $this->em
                    ->createQueryBuilder()
                    ->select('n')
                    ->from('PiggyBoxShopBundle:Category', 'n')
                    ->where('n.root = '.$this->basedOnNode->getRoot())
                    ->andWhere($qb->expr()->between(
                        'n.lft',
                        $this->basedOnNode->getLft(),
                        $this->basedOnNode->getRgt()
                    ))
                    ->getDQL()
            ));
        }
        $q = $qb->getQuery();

        return $q->getResult();
    }

    /**
     * {@inheritDoc}
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $q = $this->em
            ->createQueryBuilder()
            ->select('c')
            ->from('PiggyBoxShopBundle:Category', 'c')
            ->where($q->expr()->in(
                'c.'.$identifier,
                ':ids'
            ))
            ->setParameter('ids', $values, Connection::PARAM_INT_ARRAY)
            ->getQuery()
        ;

        return $q->getResult();
    }
}
