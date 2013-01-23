<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PiggyBox\ShopBundle\Entity\Product;
use PiggyBox\ShopBundle\Entity\Day;
use PiggyBox\ShopBundle\Entity\Shop;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GenerateDayCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('piggybox:generate:days')
            ->setDescription('Generates day for the shops')
            ->addArgument('shopSlug', InputArgument::REQUIRED, 'What is the slug of the shop')		
            ->addArgument('user', InputArgument::REQUIRED, 'user');		
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');		
        $userSlug = $input->getArgument('user');		
		$user = $em->getRepository('PiggyBoxUserBundle:User')->findOneByUsername($userSlug);
			
		$providerKey = $this->getContainer()->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
		$this->getContainer()->get('security.context')->setToken($token);

        $shopSlug = $input->getArgument('shopSlug');
        $shop = $em->getRepository('PiggyBoxShopBundle:Shop')->findOneBySlug($shopSlug);

		$dialog = $this->getHelperSet()->get('dialog');
		


        $monday = new Day();
        $monday->setDayOfTheWeek(1);
		$opened = $dialog->ask(
    		$output,
    		'Le magasin est-il ouvert le Lundi? (y or n (default y))',
    		true
		);
		if ($opened == 'y') {
			$opened = true;			
		}
		if ($opened == 'n') {
			$opened = false;
		}
		$monday->setOpen($opened); 
		$fromTimeMorning = $dialog->ask(
    		$output,
    		'A partir de quelle heure ouvre t-il (e.g 06:30)',
			null
		);		
		$monday->setFromTimeMorning(new \DateTime($fromTimeMorning));
		$monday->setToTimeMorning(new \DateTime('21:00'));
		//$monday->setFromTimeAfternoon();
		//$monday->setToTimeAfternoon();
			
		$tuesday = new Day();
        $tuesday->setDayOfTheWeek(2);
		$tuesday->setOpen(true); 
		$tuesday->setFromTimeMorning(new \DateTime('06:30'));
		$tuesday->setToTimeMorning(new \DateTime('21:00'));
	//	$tuesday->setFromTimeAfternoon();
	//	$tuesday->setToTimeAfternoon();
			
		$wednesday = new Day();
        $wednesday->setDayOfTheWeek(3);
		$wednesday->setOpen(false); 
		$wednesday->setFromTimeMorning(new \DateTime('06:30'));
		$wednesday->setToTimeMorning(new \DateTime('21:00'));
	//	$wednesday->setFromTimeAfternoon();
	//	$wednesday->setToTimeAfternoon();
			
		$thursday = new Day();
        $thursday->setDayOfTheWeek(4);
		$thursday->setOpen(true); 
		$thursday->setFromTimeMorning(new \DateTime('06:30'));
		$thursday->setToTimeMorning(new \DateTime('21:00'));
	//	$thursday->setFromTimeAfternoon();
	//	$thursday->setToTimeAfternoon();
			
		$friday = new Day();
        $friday->setDayOfTheWeek(5);
		$friday->setOpen(true); 
		$friday->setFromTimeMorning(new \DateTime('06:30'));
		$friday->setToTimeMorning(new \DateTime('21:00'));
	//	$friday->setFromTimeAfternoon();
	//	$friday->setToTimeAfternoon();
			
		$saturday = new Day();
        $saturday->setDayOfTheWeek(6);
		$saturday->setOpen(true); 
		$saturday->setFromTimeMorning(new \DateTime('06:30'));
		$saturday->setToTimeMorning(new \DateTime('21:00'));
	//	$saturday->setFromTimeAfternoon();
	//	$saturday->setToTimeAfternoon();

        $sunday = new Day();
        $sunday->setDayOfTheWeek(7);
		$sunday->setOpen(true); 
		$sunday->setFromTimeMorning(new \DateTime('06:30'));
		$sunday->setToTimeMorning(new \DateTime('21:00'));
	//	$sunday->setFromTimeAfternoon();
	//	$sunday->setToTimeAfternoon();

        $shop->addOpeningDay($monday);
        $shop->addOpeningDay($tuesday);
        $shop->addOpeningDay($wednesday);
        $shop->addOpeningDay($thursday);
        $shop->addOpeningDay($friday);
        $shop->addOpeningDay($saturday);
        $shop->addOpeningDay($sunday);

        $em->persist($shop);
        $em->flush();
    }
}
//Bux => Lundi au dimanche sauf mercredi de 6H30 à 21H00
//La garenne => Lundi à samedi 6H30 à 20H30
//Grand large => Lundi au samedi, 6H30 à 20H00
//Futuroscope => Lundi à Samedi, 6H30 à 20H30


