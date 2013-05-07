<?php

namespace PiggyBox\SitemapBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

class GenerateCommand extends DoctrineCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('piggybox:generate:sitemap')
            ->setDescription('Generate Côtelettes & Tarte aux Fraises sitemap.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $c = $this->getContainer();

        $shops = $em->createQuery('SELECT s, p, c FROM PiggyBoxShopBundle:Shop s LEFT JOIN s.products p LEFT JOIN p.category c')
            ->getResult();

        if (!$c->hasParameter('piggy_box_sitemap.base_url')) {
            throw new \RuntimeException("Sitemap requires base_url parameter [piggy_box_sitemap.base_url] to be available, through config or parameters");
        }

        $output->write('<info>Fetching resources..</info>' . PHP_EOL);

        $sitemapFile = $c->getParameter('kernel.root_dir').'/../web/sitemap.xml';
        $output->write('<info>Building sitemap...</info>' . PHP_EOL);
        $tpl = 'PiggyBoxSitemapBundle::sitemap.xml.twig';
        $sitemap = $c->get('templating')->render($tpl, compact('shops'));
        $output->write("<info>Saving sitemap in [{$sitemapFile}]..</info>" . PHP_EOL);
        file_put_contents($sitemapFile, $sitemap);
        // gzip the sitemap
        if (function_exists('gzopen')) {
            $output->write("<info>Gzipping the generated sitemap [{$sitemapFile}.gz]..</info>" . PHP_EOL);
            $gz = gzopen($sitemapFile.'.gz', 'w9');
            gzwrite($gz, $sitemap);
            gzclose($gz);
        }

        $output->write('<info>Done</info>' . PHP_EOL);
    }
}
