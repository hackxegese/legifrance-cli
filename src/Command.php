<?php

namespace Legifrance;

use Symfony\Component\Console\Command\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    protected $parser;

    public function __construct(\Legifrance\Parser $parser)
    {
        parent::__construct();

        $this->parser = $parser;
    }
}
