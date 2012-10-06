<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Form\EventListener\AddOpeningHoursFieldsSubscriber;
use PiggyBox\OrderBundle\Form\Type\UserType;
use Doctrine\ORM\EntityRepository; 

class OrderType extends AbstractType
{
	public function __construct($id)
	{
		$this->id = $id;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$id = $this->id;

		$builder->add('day', 'entity', 
			array (
				'label' => 'NomDuLabel',
				'class' => 'PiggyBox\\ShopBundle\\Entity\\Day',
				'property' => 'day_of_the_week',
				'query_builder' => function(EntityRepository $er) use ($id){
				      return $er->createQueryBuilder('d')
				                ->where('d.shop = ?1')
				                ->setParameter(1, $id);
				      },)
				  );	
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\Order'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_ordertype';
    }
}
