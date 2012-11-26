<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiggyBox\ShopBundle\Entity\Product;

class GenerateProductPriceCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('piggybox:generate:productprice')
            ->setDescription('Re-generate all product\'s prices');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $products = $em->getRepository('PiggyBoxShopBundle:Product')->findAll();

        foreach ($products as $product) {
			if ($product->getPriceType() == 'unit_variable_price') {
				$product->setPrice(round($product->getProductWeightPerSlice()*$product->getWeightPrice()/1000, 2, PHP_ROUND_HALF_UP));
			}
			if ($product->getPriceType() == 'chunk_price') {
				$product->setPrice(round($product->getMinWeight()*$product->getWeightPrice()/1000, 2, PHP_ROUND_HALF_UP));
				$product->setPrice($product->getMinWeight()*$product->getWeightPrice()/1000);
			}

            $product->setSlug(null);
            $em->persist($product);
            $em->flush();
        }
    }
}

