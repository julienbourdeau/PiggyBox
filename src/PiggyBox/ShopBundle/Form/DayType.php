<?php

namespace PiggyBox\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('open')
            ->add('from_time_morning')
            ->add('to_time_morning')
            ->add('from_time_afternoon')
            ->add('to_time_afternoon')
            ->add('day_of_the_week')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\ShopBundle\Entity\Day'
        ));
    }

    public function getName()
    {
        return 'day';
    }
}
