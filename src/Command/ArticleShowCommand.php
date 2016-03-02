<?php
declare(strict_types = 1);

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticleShowCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('article:show')
            ->setDescription('Affiche une section')
            ->addOption('code', null, InputArgument::REQUIRED, 'L\'ID du code')
            ->addOption('section', null, InputArgument::REQUIRED, 'L\'ID de la section')
            ->addOption('article', null, InputArgument::REQUIRED, 'L\'ID de l\'article')
            ->addOption('date', date('Ymd'), InputArgument::OPTIONAL, 'Date du code, sous la format YYYYMMDD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parser->setDate(
            $input->getOption('date')
        );
        $article = $this->parser->getArticle(
            $input->getOption('code'),
            $input->getOption('section'),
            $input->getOption('article')
        );
        if (!empty($article)) {
            $output->writeln("<comment>{$article['title']}</comment>");
            $output->writeln($article['content']);
        }
    }
}
