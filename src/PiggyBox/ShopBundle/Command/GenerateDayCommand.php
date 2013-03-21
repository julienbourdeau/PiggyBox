<?php

namespace PiggyBox\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

        $openingDays = $shop->getOpeningDays();
        foreach ($openingDays as $day) {
            $shop->removeOpeningDay($day);
            $em->remove($day);
        }
        $em->persist($shop);
        $em->flush();

        $dialog = $this->getHelperSet()->get('dialog');

        for ($i = 1; $i < 8; $i++) {
            $monday = new Day();
            $monday->setDayOfTheWeek($i);
            $opened = $dialog->ask(
                $output,
                'Le magasin est-il ouvert le jour '.$i.'? (y or n (default y))',
                true
            );
            if ($opened === 'y') {
                $opened = true;
            }
            if ($opened === 'n') {
                $opened = false;
            }
            $monday->setOpen($opened);
            $fromTimeMorning = $dialog->ask(
                $output,
                'A partir de quelle heure ouvre t-il (e.g 06:30 null si ENTER)',
                null
            );
            $monday->setFromTimeMorning(new \DateTime($fromTimeMorning));
            $toTimeMorning = $dialog->ask(
                $output,
                'A partir de quelle heure ferme t-il le matin (e.g 12:30)',
                null
            );
            $monday->setToTimeMorning(new \DateTime($toTimeMorning));
            $setFromTimeAfternoon = $dialog->ask(
                $output,
                'A partir de quelle heure ouvre t-il l\'après-midi ? (e.g 13:30)',
                null
            );
            $monday->setFromTimeAfternoon(new \DateTime($setFromTimeAfternoon));
            $setToTimeAfternoon = $dialog->ask(
                $output,
                'A partir de quelle heure ferme t-il le soir? (e.g 21:30)',
                null
            );
            $monday->setToTimeAfternoon(new \DateTime($setToTimeAfternoon));

        $shop->addOpeningDay($monday);
        }

        $em->persist($shop);
        $em->flush();
    }
}
