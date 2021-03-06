<?php

namespace PiggyBox\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DayRepository extends EntityRepository
{
    public function getDayDetailsFromShop($shop_id, $day_of_the_week)
    {
    return $this->getEntityManager()
            ->createQuery('SELECT d FROM PiggyBoxShopBundle:Day d  WHERE (d.shop=:shop_id AND d.open=1 AND d.day_of_the_week=:day_of_the_week)')
            ->setParameter('shop_id', $shop_id)
            ->setParameter('day_of_the_week', $day_of_the_week)
            ->getSingleResult();
            }

}
