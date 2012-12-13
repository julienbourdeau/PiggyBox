<?php

namespace PiggyBox\ShopBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MenuDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formFactory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formFactory) {
				$data = $event->getData();

                if (null === $event->getData()) {
                     return;
                }

                if (!$data) {
                    $event->getForm()->add(
                        $formFactory->createNamed('products', 'entity', null, array(
							'class'         => 'PiggyBox\ShopBundle\Entity\Product',
							'property'      => 'name',
							'label'         => 'Produits',
							'multiple'		=> false,
							'expanded'		=> true,
							'query_builder' => function (EntityRepository $repository) use ($data) {
								$qb = $repository->createQueryBuilder('product')
									->where('product.shop= :shop')
									->setParameter('shop', null);
								return $qb;
		 					}
					))
					);
                }
				
                if ($data->getMenu() != null) {
					foreach ($data->getMenu()->getMenuItems() as $menuItem) {
						$event->getForm()->add(
							$formFactory->createNamed('products_'.$menuItem->getId(), 'entity', null, array(
								'class'         => 'PiggyBox\ShopBundle\Entity\Product',
								'property'      => 'name',
								'label'         => 'Produits',
								'multiple'		=> false,
								'expanded'		=> true,
								'property_path' => false,
								'query_builder' => function (EntityRepository $repository) use ($data, $menuItem){
									$qb = $repository->createQueryBuilder('product');
									$qb->select(array('DISTINCT p', 'i'))
										->from('PiggyBoxShopBundle:Product', 'p')
										->leftJoin('p.menuItems', 'i')
										->where('(p.shop=?1 AND i.id=?2)')
										->setParameters(array(1 => $data->getMenu()->getShop()->getId(), 2 => $menuItem->getId()));
									return $qb;
								}
						))
					);
					}
                }
			});		
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\MenuDetail'
        ));
    }

    public function getName()
    {
        return 'piggybox_shopbundle_menudetailtype';
    }
}
