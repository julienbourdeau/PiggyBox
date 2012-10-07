<?php

// src/Acme/TaskBundle/Form/DataTransformer/IssueToNumberTransformer.php
namespace PiggyBox\OrderBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use PiggyBox\ShopBundle\Entity\Day;

class DayToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($day)
    {
        if (null === $day) {
            return "";
        }

        return $day->getId();
    }

    /**
     * Transforms a string (number) to an object (day).
     *
     * @param  string $number
     * @return Day|null
     * @throws TransformationFailedException if object (day) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $day = $this->om
            ->getRepository('PiggyBoxShopBundle:Day')
				->findOneBy(array('id' => $number))
        ;

        if (null === $day) {
            throw new TransformationFailedException(sprintf(
				'A day with number "%s" does not exist!',
                $number
            ));
        }

        return $day;
    }
}