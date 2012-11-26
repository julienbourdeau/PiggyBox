<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\ShopBundle\Entity\Category;

class GenerateCategoryHtmlCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('piggybox:generate:htmlcategories')
            ->setDescription('Re-generate and all categories tree for shops');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $shops = $em->getRepository('PiggyBoxShopBundle:Shop')->findAll();

        foreach ($shops as $shop) {
            $categories = $em->createQuery('SELECT DISTINCT c, p FROM PiggyBoxShopBundle:Category c JOIN c.products p  WHERE p.shop=:id')
                                      ->setParameter('id', $shop->getId())
                                      ->getResult();

            $categories_buffer = new \Doctrine\Common\Collections\ArrayCollection();
            $html = '';

            foreach ($categories as $category) {
                if (!$categories_buffer->contains($category)) {
                    if ($category->getLevel() !=0) {
                        $category = $category->getParent();
                    }

                    $html.="\t".'<li id="'.$category->getSlug().'" class="">'."\r\n";
                    $html.="\t\t".'<a href="'.$this->getContainer()->get('router')->generate('user_show_shop',array('slug' => $shop->getSlug(), 'category_title' => $category->getTitle())).'" class="category-item '.$category->getTitle().'">'.$category->getTitle().'</a>'."\r\n";

                    $children_categories = $category->getChildren();
                    if (count($children_categories) > 0) {
                        $html.="\t\t".'<ul class="nav nav-list subnav">'."\r\n";

                        foreach ($children_categories as $children_category) {
                            if (in_array($children_category, $categories) and !$categories_buffer->contains($children_category)) {
                                $html.="\t\t\t".'<li id="'.$children_category->getSlug().'" class="">'."\r\n";
                                    $html.="\t\t\t\t".'<a href="'.$this->getContainer()->get('router')->generate('user_show_shop',array('slug' => $shop->getSlug(), 'category_title' => $children_category->getTitle())).'" class="category-item '.$children_category->getTitle().' ">'.$children_category->getTitle().'</a>'."\r\n";
                                $html.="\t\t\t".'</li>'."\r\n";
                                $categories_buffer->add($children_category);
                            }
                        }

                        $categories_buffer->add($category);
                        $html.="\t\t".'</ul>'."\r\n";
                    }

                    $html.="\t".'</li>'."\r\n";

                }
            }
            $shop->setCategoryHtml($html);
            $em->persist($shop);
            $em->flush();
        }

        $output->writeln('<info>Categories generated for all shops</info>');
    }
}
