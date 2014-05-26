<?php

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('dump')
            ->setDescription('Télécharge l\'ensemble d\'un code')
            ->addOption('code', null, InputArgument::OPTIONAL, 'L\'ID du code')
            ->addOption('date', 'd', InputArgument::OPTIONAL, 'Date du code, sous la format YYYYMMDD')
            ->addArgument('destination', InputArgument::REQUIRED, 'Répertoire de destination');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dest = $input->getArgument('destination');
        $date = $input->getOption('date');
        $code = $input->getOption('code');
        if (is_null($code)) {
            $codes = $this->parser->getCodes();
        }
        else {
            $codes = [$code => ''];
        }
        $this->mkdir($dest);

        $this->parser->setDate($date);

        foreach ($codes as $codeId => $codeTitle) {
            $codeTitle = $this->parser->getCodeTitle($codeId);

            $output->writeln("<comment>$codeId</comment> ($codeTitle) at $date");
            $this->mkdir("$dest/$codeId");

            file_put_contents("$dest/README.md", "# legifrance.git\n\n");
            file_put_contents("$dest/README.md", "* [$codeTitle](./$codeId/README.md)\n", FILE_APPEND);

            file_put_contents("$dest/$codeId/README.md", "# $codeTitle\n\n");

            $sections = $this->parser->getSummary($codeId);
            foreach ($sections as $sectionId => $section) {
                $this->mkdir("$dest/$codeId/$sectionId");
                file_put_contents("$dest/$codeId/README.md", "* [{$section['title']}](./$sectionId/README.md)\n", FILE_APPEND);
                file_put_contents("$dest/$codeId/$sectionId/README.md", "# {$section['title']}\n\n");

                $acticles = $this->parser->getSection($codeId, $sectionId);
                foreach ($acticles as $articleId => $articleTitle) {
                    $article = $this->parser->getArticle($codeId, $sectionId, $articleId);
                    if (isset($article['content'])) {
                        file_put_contents("$dest/$codeId/$sectionId/README.md", "* [{$article['title']}](./$articleId.md)\n", FILE_APPEND);
                        file_put_contents("$dest/$codeId/$sectionId/$articleId.md", "# {$article['title']}\n\n{$article['content']}");
                    }
                }
            }
        }
    }

    private function mkdir($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }
}
