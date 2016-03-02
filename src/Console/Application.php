<?php
declare(strict_types = 1);

namespace Legifrance\Console;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct('legifrance');

        $stream = new \Legifrance\Stream();
        $parser = new \Legifrance\Parser($stream);

        $this->addCommands([
            new \Legifrance\Command\CodeListCommand($parser),
            new \Legifrance\Command\CodeShowCommand($parser),
            new \Legifrance\Command\CodePatchCommand($parser),
            new \Legifrance\Command\SectionShowCommand($parser),
            new \Legifrance\Command\ArticleShowCommand($parser),
            new \Legifrance\Command\DumpCommand($parser),
        ]);
    }
}
