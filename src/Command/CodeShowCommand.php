<?php
declare(strict_types = 1);

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeShowCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('code:show')
            ->setDescription('Affiche le sommaire d\'un code')
            ->addOption('code', null, InputOption::VALUE_REQUIRED, 'L\'ID du code')
            ->addOption('date', date('Ymd'), InputOption::VALUE_OPTIONAL, 'Date du code, sous la format YYYYMMDD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->parser->setDate(
            $input->getOption('date')
        );
        $summary = $this->parser->getSummary(
            $input->getOption('code')
        );
        foreach ($summary as $id => $entry) {
            $output->write(str_repeat(' ', $entry['level'] * 2));
            $output->writeln("<comment>$id</comment> {$entry['title']}");
        }
    }
}
