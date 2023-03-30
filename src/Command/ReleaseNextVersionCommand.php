<?php
declare(strict_types=1);

namespace Larsnieuwenhuizen\CiHelpers\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReleaseNextVersionCommand extends DefineVersionCommand
{

    const CHANGELOG_PATH = "/app/code/CHANGELOG.md";

    private bool $changelogUpdate = false;

    public function __construct(string $name = 'version:release')
    {
        parent::__construct($name);
        $this->setDescription('Get the next version and create the release commit');
    }

    protected function configure()
    {
        $this->addOption(
            'changelog-update',
            'c',
            InputOption::VALUE_NEGATABLE,
            'should the changelog be updated? Defaults to false',
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $nextVersion = $this->nextVersion();
            if ($this->io->isVerbose()) {
                $this->io->title('Retrieving next version');
                $this->io->text($nextVersion);
                $this->io->note('Creating release commit');
            }

            if ($input->getOption('changelog-update') === true) {
                $this->updateChangelog($nextVersion);
            }

            $this->createReleaseCommit($nextVersion, $input->getOption('changelog-update'));
        } catch (Exception $exception) {
            $this->io->error($exception->getMessage());
        }
        exit(0);
    }

    private function createReleaseCommit(string $versionTag, bool $changelogUpdate = false): void
    {
        if ($changelogUpdate === true) {
            \shell_exec('cd /app/code && git add CHANGELOG.md');
        }

        \shell_exec('cd /app/code && git commit --allow-empty -m "release: ' . $versionTag . '"');
        \shell_exec('cd /app/code && git tag ' . $versionTag . ' -m "' . $versionTag . '"');
        \shell_exec('cd /app/code && git push origin master --tags');
    }

    private function updateChangelog(string $version): void
    {
        $this->io->isVerbose() ?? $this->io->title('Updating the changelog');
        $lastTag = $this->getLastTag();
        $commits = \explode(
            "\n",
            $this->getCommitsSinceLastTag($lastTag)
        );

        if (\file_exists(self::CHANGELOG_PATH) === false) {
            touch(self::CHANGELOG_PATH);
            file_put_contents(self::CHANGELOG_PATH, "# Changelog \n## Version history\n");
        }

        $commitLines = "## Version history\n### $version | " . \date('d-m-Y') . "\n\n";
        foreach ($commits as $commit) {
            $commitLines .= "$commit\n";
        }
        $commitLines .= "\n";

        $changelog = \file_get_contents(self::CHANGELOG_PATH);
        $newChangelogContents = \str_replace("## Version history\n", $commitLines, $changelog);

        \file_put_contents(self::CHANGELOG_PATH, $newChangelogContents);
    }
}
