<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formFactory) {

                if (null === $event->getData()) {
                     return;
                }

                if ($event->getData()) {
                    var_dump($event->getData()->getMenu()->getShop()->getId());die();
                    $event->getForm()->add(
                        $formFactory->createNamed('products', 'entity', null, array(
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
					))
					);
                }
            }
        );

        $builder
            ->add('title')
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
