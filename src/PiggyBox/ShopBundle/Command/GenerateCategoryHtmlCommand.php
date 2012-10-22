<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
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

			foreach($categories as $category){
				if(!$categories_buffer->contains($category))
				{
					if($category->getLevel() !=0){
						$category = $category->getParent();
					}
				
					$html.='<li class=""><a href="'.$this->getContainer()->get('router')->generate('user_show_shop',array('slug' => $shop->getSlug(), 'category_title' => $category->getTitle())).'" class="category-item'.$category->getTitle().'">'.$category->getTitle().'</a></li>';

					$children_categories = $category->getChildren();
					$html.='<ul class="nav nav-list">';

					foreach ($children_categories as $children_category) {
						if(in_array($children_category, $categories) and !$categories_buffer->contains($children_category))
						{
							$html.='<li class=""><a href="'.$this->getContainer()->get('router')->generate('user_show_shop',array('slug' => $shop->getSlug(), 'category_title' => $children_category->getTitle())).'" class="category-item '.$children_category->getTitle().' ">'.$children_category->getTitle().'</a></li>';
							$categories_buffer->add($children_category);
						}
					}
					$categories_buffer->add($category);
					$html.='</ul>';	
				}		
			}
			$shop->setCategoryHtml($html);
			$em->persist($shop);
			$em->flush();
		}
    }
}

