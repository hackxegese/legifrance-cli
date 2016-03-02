<?php
declare(strict_types = 1);

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SectionShowCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('section:show')
            ->setDescription('Affiche une section')
            ->addOption('code', null, InputArgument::REQUIRED, 'L\'ID du code')
            ->addOption('section', null, InputArgument::REQUIRED, 'L\'ID de la section')
            ->addOption('date', date('Ymd'), InputArgument::OPTIONAL, 'Date du code, sous la format YYYYMMDD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parser->setDate(
            $input->getOption('date')
        );
        $section = $this->parser->getSection(
            $input->getOption('code'),
            $input->getOption('section')
        );
        foreach ($section as $id => $entry) {
            $output->writeln("<comment>$id</comment> $entry");
        }
    }
}
