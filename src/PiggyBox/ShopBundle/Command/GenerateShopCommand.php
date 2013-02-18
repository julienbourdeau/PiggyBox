<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PiggyBox\ShopBundle\Entity\Shop;
use PiggyBox\ShopBundle\Entity\Category;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class GenerateShopCommand extends ContainerAwareCommand
{

    protected function configure()
    {
		$this
            ->setName('piggybox:generate:shop')
            ->setDescription('Generate Shop\'s information from it\'s slug')
            ->addArgument(
                'shop_slug',
                InputArgument::REQUIRED,
                'Which shop needs a description generation'
            )
			->addOption('phone_number', null, InputOption::VALUE_REQUIRED, 'Le numéro de téléphone du commerce')
            ->setHelp(<<<EOT
The <info>piggybox:generate:shop</info> task generates a new Shop
entity in the database and also in all twig files that need to be updated
EOT
        );
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		// Prend un magasin en argument
		// ajoute toutes les infos de type tab-info, carousel, etc...
		// TODO: appeller la génération de catégories

		// Demander les informations pour chaque paramètre
		$dialog = $this->getHelperSet()->get('dialog');
		

		$phone_number = $dialog->ask(
		    $output,
		    '<info>Quelle est le numéro de téléphone du commerce</info> <error>(si une valeur est donnée, l\'ancienne valeur sera réecrite)</error>',
			null
		);
		
		$shop_slug = $input->getArgument('shop_slug');
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

		$shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($shop_slug);
		// Fichier à écrire: tab-fiche.html.twig, tab-infos, tab-photos, tab-shop-infos, tab-slider

		$finder = new Finder();

		// fichier tab-fiche.html.twig
		$finder->files()->in(__DIR__.'/../../UserBundle/Resources/views/User/');

		foreach ($finder->name('tab-fiche.html.twig') as $file) {
			if ($file->getRelativePathname() == 'tab-fiche.html.twig') {
				$tab = file($file->getRealpath());
				if (!in_array('{% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab)) {
					$key = array_search('{% endif %}'."\n", $tab);

					array_splice( $tab, $key, 0, array("\n"));				
					array_splice( $tab, $key+1, 0, array("{% elseif (shop.slug == '".$shop_slug."') %}\n"));
					array_splice( $tab, $key+2, 0, array("\n"));
					array_splice( $tab, $key+3, 0, array("<h2>".$shop->getName()."</h2>\n"));
					array_splice( $tab, $key+4, 0, array("\n"));
					array_splice( $tab, $key+5, 0, array("<div class=\"row-fluid\">\n"));
					array_splice( $tab, $key+6, 0, array("\n"));
					array_splice( $tab, $key+7, 0, array("\t<div class=\"span12\">\n"));
					array_splice( $tab, $key+8, 0, array("\t\t<h4>".$shop->getName()."</h4>\n"));
					array_splice( $tab, $key+9, 0, array("\n"));
					array_splice( $tab, $key+10, 0, array("\t\t<p>Présentation du magasin à rédiger.</p>\n"));
					array_splice( $tab, $key+11, 0, array("\t</div>\n"));
					array_splice( $tab, $key+12, 0, array("\n"));
					array_splice( $tab, $key+13, 0, array("</div>\n"));

					file_put_contents ($file->getRealpath() , $tab);
				}
			}
		}

		foreach ($finder->name('tab-infos.html.twig') as $file) {
			if ($file->getRelativePathname() == 'tab-infos.html.twig') {
				$tab = file($file->getRealpath());
				if (!in_array('{% if (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab)) {
					$key = array_search('{% endif %}'."\n", $tab);

					array_splice($tab, $key, 0, array("\n"));				
					array_splice($tab, $key+1, 0, array("{#\n"));				
					array_splice($tab, $key+2, 0, array("#################################################################################################\n"));				
					array_splice($tab, $key+3, 0, array("#####\n"));				
					array_splice($tab, $key+4, 0, array("##### \t\t\t".strtoupper($shop->getName())."\n"));				
					array_splice($tab, $key+5, 0, array("#####\n"));				
					array_splice($tab, $key+6, 0, array("#################################################################################################\n"));				
					array_splice($tab, $key+7, 0, array("#}\n"));				
					array_splice($tab, $key+8, 0, array("{% elseif (shop.slug == '".$shop_slug."') %}\n"));				
					array_splice($tab, $key+9, 0, array("\n"));				
					array_splice($tab, $key+10, 0, array("<h2>".$shop->getName().": horaires, adresse & détails</h2>\n"));				
					array_splice($tab, $key+11, 0, array("\n"));				
					array_splice($tab, $key+12, 0, array("\n"));				
					array_splice($tab, $key+13, 0, array("<div class=\"row-fluid\">\n"));				
					array_splice($tab, $key+14, 0, array("\n"));				
					array_splice($tab, $key+15, 0, array("\t<div class=\"span6\">\n"));				
					array_splice($tab, $key+16, 0, array("\t\t<h4>Parking</h4>\n"));				
					array_splice($tab, $key+17, 0, array("\t\t<p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_parking_logo.png')}}\" alt=\"\"> Grand parking <strong>gratuit</strong> à proximité</p>\n"));				
					array_splice($tab, $key+18, 0, array("\n"));				
					array_splice($tab, $key+19, 0, array("\t\t<h4>Paiements acceptés</h4>\n"));				
					array_splice($tab, $key+20, 0, array("\t\t<p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_cb_logo.png')}}\" alt=\"\"> Carte bleue (sans minimum)</p>\n"));				
					array_splice($tab, $key+21, 0, array("\t\t<p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_cheque_resto_logo.png')}}\" alt=\"\"> Tickets restaurant (tout type)</p>\n"));				
					array_splice($tab, $key+22, 0, array("\n"));				
					array_splice($tab, $key+23, 0, array("\n"));				
					array_splice($tab, $key+24, 0, array("\t</div>\n"));				
					array_splice($tab, $key+25, 0, array("\n"));				
					array_splice($tab, $key+26, 0, array("\t<div class=\"span6\">\n"));				
					array_splice($tab, $key+27, 0, array("\t\t<h4>Coordonnées</h4>\n"));				
					array_splice($tab, $key+28, 0, array("\n"));				
					array_splice($tab, $key+29, 0, array("        <p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_tel_logo.png')}}\" alt=\"\"> 02.40.46.50.42</p>\n"));				
					array_splice($tab, $key+30, 0, array("        <p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_email_logo.png')}}\" alt=\"\"> zola@boucherdefrance.fr</p>\n"));				
					array_splice($tab, $key+31, 0, array("        <p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_addr_logo.png')}}\" alt=\"\"> 6 place Emile Zola 44100 Nantes (<a href=\"#map-large\">Carte</a>)</p>\n"));				
					array_splice($tab, $key+32, 0, array("\n"));				
					array_splice($tab, $key+33, 0, array("\n"));				
					array_splice($tab, $key+34, 0, array("\t\t<h4>Transport</h4>\n"));				
					array_splice($tab, $key+35, 0, array("\t\t<p><img src=\"{{ asset('bundles/piggyboxuser/img/icons/new_tan_logo.png')}}\" alt=\"\"> Bus 11, 23, 54, 70 - Arrêt \"<em><strong>Place Zola</strong></em>\"\n"));				
					array_splice($tab, $key+36, 0, array("\t\t</p>\n"));				
					array_splice($tab, $key+37, 0, array("\t</div>\n"));				
					array_splice($tab, $key+38, 0, array("\n"));				
					array_splice($tab, $key+39, 0, array("</div>\n"));				
					array_splice($tab, $key+40, 0, array("\n"));				
					array_splice($tab, $key+41, 0, array("\n"));				
					array_splice($tab, $key+42, 0, array("<div class=\"row-fluid\">\n"));				
					array_splice($tab, $key+43, 0, array("\t<div id=\"map-large\" class=\"span12\">\n"));				
					array_splice($tab, $key+44, 0, array("\t\t<h3>La carte <small>".$shop->getName()." - Nantes</small></h3>\n"));				
					array_splice($tab, $key+45, 0, array("Mettre la google map"));				
					array_splice($tab, $key+46, 0, array("\t</div>\n"));				
					array_splice($tab, $key+47, 0, array("</div>\n"));				
					array_splice($tab, $key+48, 0, array("\n"));				
					array_splice($tab, $key+49, 0, array("\n"));				
					array_splice($tab, $key+50, 0, array("\n"));				

					file_put_contents ($file->getRealpath() , $tab);
				}
			}
    	}

		// fichier de tab-photo.html.twig
		foreach ($finder->name('tab-photo.html.twig') as $file) {
			if ($file->getRelativePathname() == 'tab-photo.html.twig') {

				$fs = new Filesystem();
				if (!$fs->exists(__DIR__.'/../../UserBundle/Resources/public/img/carousel/'.$shop_slug)) {
					$fs->mkdir(__DIR__.'/../../UserBundle/Resources/public/img/carousel/'.$shop_slug, 0755);
				}


				$tab = file($file->getRealpath());
				if (!in_array('{% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab)) {
					$key = array_search('{% endif %}'."\n", $tab);

					array_splice( $tab, $key, 0, array("\n"));				
					array_splice( $tab, $key+1, 0, array("{% elseif (shop.slug == '".$shop_slug."') %}\n"));
					array_splice( $tab, $key+2, 0, array("\n"));
					array_splice( $tab, $key+3, 0, array("\n"));
					array_splice( $tab, $key+4, 0, array("{% for count in range(1, 8) %}\n"));
					array_splice( $tab, $key+5, 0, array("\t<div class=\"\">\n"));
					array_splice( $tab, $key+6, 0, array("\t\t{% set path = \"bundles/piggyboxuser/img/carousel/".$shop_slug."/".$shop_slug."-\" ~ count ~ \".jpg\" %}\n"));
					array_splice( $tab, $key+7, 0, array("\t\t<img src=\"{{ asset(path) }}\" alt=\"\">\n"));
					array_splice( $tab, $key+8, 0, array("\t\t<hr />\n"));
					array_splice( $tab, $key+9, 0, array("\t</div>\n"));
					array_splice( $tab, $key+10, 0, array("{% endfor %}\n"));

					file_put_contents ($file->getRealpath() , $tab);
				}
			}
		}

		// fichier de tab-shop-infos.html.twig
		foreach ($finder->name('tab-shop-infos.html.twig') as $file) {
			if ($file->getRelativePathname() == 'tab-shop-infos.html.twig') {

				$tab = file($file->getRealpath());
				if (!in_array('    {% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab)) {
					var_dump("noooo");
					var_dump($tab);
					
					$key = array_search('    {% endif %}'."\n", $tab);

					array_splice( $tab, $key, 0, array("\n"));				
					array_splice( $tab, $key+1, 0, array("    {% elseif (shop.slug == '".$shop_slug."') %}\n"));
					array_splice( $tab, $key+2, 0, array("    <h1 class=\"\">".$shop->getName()."</h1>\n"));
					array_splice( $tab, $key+3, 0, array("\n"));
					array_splice( $tab, $key+4, 0, array("    <div id=\"shop-details\" class=\"row-fluid\">\n"));
					array_splice( $tab, $key+5, 0, array("\n"));
					array_splice( $tab, $key+6, 0, array("        <div class=\"span12\">\n"));
					array_splice( $tab, $key+7, 0, array("            <p>\n"));
					array_splice( $tab, $key+8, 0, array("                <i class=\"icon-user\"></i> ".$phone_number." <br />\n"));
					array_splice( $tab, $key+9, 0, array("                <i class=\"icon-envelope\"></i> zola@boucherdefrance.fr<br />\n"));
					array_splice( $tab, $key+10, 0, array("                <i class=\"icon-home\"></i> 6 place Emile Zola 44100 Nantes\n"));
					array_splice( $tab, $key+11, 0, array("            </p>\n"));
					array_splice( $tab, $key+12, 0, array("            <p>\n"));
					array_splice( $tab, $key+13, 0, array("                <i class=\"icon-time\"></i> Le Mardi, Mercredi, Vendredi, Samedi <br />\n"));
					array_splice( $tab, $key+14, 0, array("                <i class=\"icon-\"></i> 8:30 - 13:30 & 15:30 - 19:30 <br />\n"));
					array_splice( $tab, $key+15, 0, array("                <i class=\"icon-\"></i> Le jeudi matin <br />\n"));
					array_splice( $tab, $key+16, 0, array("                <i class=\"icon-\"></i> 8:30 - 13:15 <br />\n"));
					array_splice( $tab, $key+17, 0, array("                <i class=\"icon-\"></i> Le dimanche matin <br />\n"));
					array_splice( $tab, $key+18, 0, array("                <i class=\"icon-\"></i> 8:30 - 13:00\n"));
					array_splice( $tab, $key+19, 0, array("            </p>\n"));
					array_splice( $tab, $key+20, 0, array("\n"));
					array_splice( $tab, $key+21, 0, array("\n"));
					array_splice( $tab, $key+22, 0, array("        </div>\n"));
					array_splice( $tab, $key+23, 0, array("\n"));
					array_splice( $tab, $key+24, 0, array("\n"));
					array_splice( $tab, $key+25, 0, array("    </div>\n"));
					array_splice( $tab, $key+26, 0, array("\n"));

					file_put_contents ($file->getRealpath() , $tab);
				}

				if (in_array('    {% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab) | $phone_number != null) {
					var_dump($phone_number);
					$key = array_search('    {% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab);


					var_dump($tab[$key]);

					$tab[$key] = "    {% elseif (shop.slug == '".$shop_slug."') %}\n";
					$tab[$key+1] = "    <h1 class=\"\">".$shop->getName()."</h1>\n";
					$tab[$key+2] = "\n";
					$tab[$key+3] = "    <div id=\"shop-details\" class=\"row-fluid\">\n";
					$tab[$key+4] = "\n";
					$tab[$key+5] = "        <div class=\"span12\">\n";
					$tab[$key+6] = "            <p>\n";
					$tab[$key+7] = "                <i class=\"icon-user\"></i> ".$phone_number." <br />\n";
					$tab[$key+8] = "                <i class=\"icon-envelope\"></i> zola@boucherdefrance.fr<br />\n";
					$tab[$key+9] = "                <i class=\"icon-home\"></i> 6 place Emile Zola 44100 Nantes\n";
					$tab[$key+10] = "            </p>\n";
					$tab[$key+11] = "            <p>\n";
					$tab[$key+12] = "                <i class=\"icon-time\"></i> Le Mardi, Mercredi, Vendredi, Samedi <br />\n";
					$tab[$key+13] = "                <i class=\"icon-\"></i> 8:30 - 13:30 & 15:30 - 19:30 <br />\n";
					$tab[$key+14] = "                <i class=\"icon-\"></i> Le jeudi matin <br />\n";
					$tab[$key+15] = "                <i class=\"icon-\"></i> 8:30 - 13:15 <br />\n";
					$tab[$key+16] = "                <i class=\"icon-\"></i> Le dimanche matin <br />\n";
					$tab[$key+17] = "                <i class=\"icon-\"></i> 8:30 - 13:00\n";
					$tab[$key+18] = "            </p>\n";
					$tab[$key+19] = "        </div>\n";
					$tab[$key+20] = "\n";
					$tab[$key+21] = "    </div>\n";

					file_put_contents ($file->getRealpath() , $tab);
				}
			}
		}

		// fichier de tab-slider.html.twig
		foreach ($finder->name('tab-slider.html.twig') as $file) {
			if ($file->getRelativePathname() == 'tab-slider.html.twig') {

				$tab = file($file->getRealpath());
				if (!in_array('                                {% elseif (shop.slug == \''.$shop_slug.'\') %}'."\n", $tab)) {
					$key = array_search("                                {% endif %}\n", $tab);

					array_splice( $tab, $key, 0, array("                                {% elseif (shop.slug == '".$shop_slug."') %}\n"));
					array_splice( $tab, $key+1, 0, array("                                    {% for count in range(1, 8) %}\n"));
					array_splice( $tab, $key+2, 0, array("                                        <div class=\"item\">\n"));
					array_splice( $tab, $key+3, 0, array("                                            {% set path = \"bundles/piggyboxuser/img/carousel/".$shop_slug."/".$shop_slug."-\" ~ count ~ \".jpg\" %}\n"));
					array_splice( $tab, $key+4, 0, array("                                            <img src=\"{{ asset(path)}}\" alt=\"\">\n"));
					array_splice( $tab, $key+5, 0, array("                                        </div>\n"));
					array_splice( $tab, $key+6, 0, array("                                    {% endfor %}\n"));

					file_put_contents ($file->getRealpath() , $tab);
				}
			}
		}
		
		
	}
}
