<?php

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeListCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('code:list')
            ->setDescription('Liste les codes disponibles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $codes = $this->parser->getCodes();
        foreach ($codes as $id => $title) {
            $output->writeln("<comment>$id</comment> $title");
        }
    }
}
