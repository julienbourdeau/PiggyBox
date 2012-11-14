<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiggyBox\ShopBundle\Entity\Product;

class GenerateProductSlugCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('piggybox:generate:productslug')
            ->setDescription('Re-generate all product\'s slugs');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $products = $em->getRepository('PiggyBoxShopBundle:Product')->findAll();

        foreach ($products as $product) {

            $product->setSlug(null);
            $em->persist($product);
            $em->flush();
        }
    }
}
