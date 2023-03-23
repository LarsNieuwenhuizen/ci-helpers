<?php
declare(strict_types=1);

namespace Larsnieuwenhuizen\CiHelpers\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseNextVersionCommand extends DefineVersionCommand
{

    public function __construct(string $name = 'version:release')
    {
        parent::__construct($name);
        $this->setDescription('Get the next version and create the release commit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $nextVersion = $this->nextVersion();
            if ($io->isVerbose()) {
                $io->title('Retrieving next version');
                $io->text($nextVersion);
                $io->note('Creating release commit');
            }
            $this->createReleaseCommit($nextVersion);
        } catch (Exception $exception) {
            $io->error($exception->getMessage());
        }
        exit(0);
    }

    private function createReleaseCommit(string $versionTag): void
    {
        \shell_exec('cd app && git commit --allow-empty -m "release: ' . $versionTag . '"');
        \shell_exec('git --git-dir=app/.git tag -s ' . $versionTag . ' -m ' . $versionTag);
    }
}
