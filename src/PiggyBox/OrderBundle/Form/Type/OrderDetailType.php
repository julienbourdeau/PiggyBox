<?php

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PiggyBox\OrderBundle\Entity\OrderDetail;
use PiggyBox\ShopBundle\Entity\Product;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class OrderDetailType extends AbstractType
{
    private $price_type;

    public function __construct($price_type)
    {
        $this->price_type = $price_type;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $price_type = $this->price_type;

        if ($price_type != Product::WEIGHT_PRICE) {
            $builder->add('quantity', 'choice', array(
                'choice_list' => new SimpleChoiceList(
                    array(
                        '1' => '1' ,
                        '2' => '2' ,
                        '3' => '3' ,
                        '4' => '4' ,
                        '5' => '5' ,
                        '6' => '6' ,
                        '7' => '7' ,
                        '8' => '8' ,
                        '9' => '9' ,
                        '8' => '8' ,
                        '10' =>'10',
                    )
                )
            ));
        }

        if ($price_type == Product::WEIGHT_PRICE) {
            $builder->add('quantity', 'choice', array(
                'choice_list' => new SimpleChoiceList(
                    array(
                        '1' => '100g' ,
                        '2' => '200g' ,
                        '3' => '300g' ,
                        '4' => '400g' ,
                        '5' => '500g' ,
                        '6' => '600g' ,
                        '7' => '700g' ,
                        '8' => '800g' ,
                        '9' => '900g' ,
                        '8' => '800g' ,
                        '10' =>'1000g',
                        '12' =>'1,2Kg',
                        '14' =>'1,4Kg',
                        '16' =>'1,6Kg',
                        '18' =>'1,8Kg',
                        '20' =>'2,0Kg',
                        '22' =>'2,2Kg',
                        '24' =>'2,4Kg',
                        '26' =>'2,6Kg',
                        '28' =>'2,8Kg',
                        '30' =>'3,0Kg',
                    )
                )
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PiggyBox\OrderBundle\Entity\OrderDetail'
        ));
    }

    public function getName()
    {
        return 'piggybox_orderbundle_orderdetailtype';
    }
}
