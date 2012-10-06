<?php 

namespace PiggyBox\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType; 
use Symfony\Component\Form\FormBuilder; 

use Doctrine\ORM\EntityRepository; 

class PickupDateTimeChoiceType extends AbstractType 
{ 
    public function buildForm(FormBuilder $builder, array $options) 
    { 
        $builder->add('region', 'entity', array( 
            'em' => 'default', 
            'class' => 'Acme\\DemoBundle\\Entity\\Region', 
            'multiple' => false, 
            'required' => true, 
            'query_builder' => function(EntityRepository $repo) { 
                return $repo->createQueryBuilder('r')->orderBy('r.name', 'ASC'); 
            }, 
        )); 

    } 

   /**
     * set parent to form cause our type will have more fields than one
     */
    public function getParent (array $options) 
    { 
        return 'form'; 
    } 

    /**
     * we can register type as service using that name as form.type tag alias 
     */
    public function getName() 
    { 
        return 'location_choice'; 
    } 
}