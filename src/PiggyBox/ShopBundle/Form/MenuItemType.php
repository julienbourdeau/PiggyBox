<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
//			->add('products', 'entity', array(
//				'class' => 'PiggyBoxShopBundle:Product',
//				'property' => 'name',
//				'multiple' => true,
//				'expanded' => true,
//			));

			->add('products', 'entity', array(
                'class'         => 'PiggyBox\ShopBundle\Entity\Product',
                'property'      => 'name',
                'label'         => 'Produits',
				'multiple'		=> true,
				'expanded'		=> true,
                'query_builder' => function (EntityRepository $repository) {
                                       $qb = $repository->createQueryBuilder('product')
													->where('product.shop= :shop')
                                                    ->setParameter('shop', 4);
                                       return $qb;
                                   }
                 ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\MenuItem'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_menuitemtype';
    }
}
