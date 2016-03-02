<?php
declare(strict_types = 1);

namespace Legifrance\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodePatchCommand extends \Legifrance\Command
{
    protected function configure()
    {
        $this->setName('code:patch')
            ->setDescription('Applique les modifications d’un projet de loi')
            ->addArgument('code', InputArgument::REQUIRED, 'Répertoire de destination')
            ->addArgument('projet', InputArgument::REQUIRED, 'Fichier du projet de loi');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getArgument('code');
        $projet = $input->getArgument('projet');

        $law = $this->loadLaw($code);

        $fd = fopen($projet, 'r');

        $currentArt = null;
        $filename = null;

        $step = Step::START;

        while ($step !== Step::STOP) {
            switch ($step) {
                case Step::START:
                    $step = Step::NEXT_LINE;
                break;
                case Step::NEXT_LINE:
                    $line = fgets($fd);
                    if ($line === false) {
                        $step = Step::STOP;
                    }
                    else {
                        $step = Step::NEW_LINE;
                    }
                break;
                case Step::NEW_LINE:
                    if ($this->isPageNumber($line)) {
                        $step = Step::NEXT_LINE;
                    }
                    elseif (
                        $this->isEndOfQuote($line)
                        || $this->isNewChapter($line)
                    ) {
                        $step = empty($currentArt) ? Step::NEXT_LINE : Step::END_OF_ART;
                    }
                    else {
                        if ($this->isStartOfArt($line)) {
                            $art = $this->getArtNum($line);
                            if (empty($currentArt)) {
                                if (isset($law[$art])) {
                                    $filename = $law[$art];
                                }
                                $step = Step::IN_ART;
                            }
                            else {
                                $step = Step::END_OF_ART;
                            }
                        }
                        else {
                            $step = empty($currentArt) ? Step::NEXT_LINE : Step::IN_ART;
                        }
                    }
                break;
                case Step::END_OF_ART:
                    $added =  ($filename === null || preg_match('/^(« )?Art\. \d+/', trim($currentArt)) === 1);

                    $currentArt = preg_replace(
                        '/^(?:« )?Art\. L?\.? ?(\d+(?:-\d+){0,3})(?:er)?\. - /',
                        "# Article L\$1\n\n\$2",
                        trim($currentArt)
                    );
                    $currentArt = preg_replace("/ +\n/", "\n", $currentArt);
                    if (!$added) {
                        file_put_contents($filename, "$currentArt\n");
                    }
                    else {
                        file_put_contents("$code/ajouts.txt", "$currentArt\n\n", FILE_APPEND);
                    }

                    $currentArt = null;
                    $filename = null;

                    $step = Step::NEW_LINE;
                break;
                case Step::IN_ART:
                    $line = preg_replace("/\n$/", ' ', $line);
                    $line = preg_replace('/^« /', "\n\n", $line);

                    $currentArt .= $line;
                    $step = Step::NEXT_LINE;
                break;
            }
        }

        fclose($fd);
    }

    private function loadLaw($code): array
    {
        $law = [];

        foreach (glob("$code/*/LEGIARTI*.md") as $filename) {
            $fd = fopen($filename, 'r');
            $heading = fgets($fd);
            try {
                $num = $this->getArtNum($heading);
                $law[$num] = $filename;
            }
            catch (\Exception $e) {
            }
            fclose($fd);
        }
        return $law;
    }

    private function isPageNumber($line): bool
    {
        return (preg_match('/^.?\d+\n$/', $line) === 1);
    }

    private function isEndOfQuote($line): bool
    {
        return (strlen($line) >= 2 && ord($line[strlen($line) - 2]) === 187);
    }

    private function isNewChapter($line): bool
    {
        return (preg_match('/(Sous-section|Section|CHAPITRE|Paragraphe) (\d+|I+)/', $line) === 1);
    }

    private function isStartOfArt($line): bool
    {
        try {
            $art = $this->getArtNum($line);
            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    private function getArtNum(string $heading): string
    {
        if ($heading[0] === '#') {
            preg_match('/# Article L(\d+(-\d+){0,3})/', $heading, $matches);
        }
        else {
            preg_match('/« Art\. L?\.? ?(\d+(-\d+){0,3})/', $heading, $matches);
        }

        if (!isset($matches[1])) {
            throw new \Exception("Invalid heading: '$heading'");
        }

        return $matches[1];
    }
}
