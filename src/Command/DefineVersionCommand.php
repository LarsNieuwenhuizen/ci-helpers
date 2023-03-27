<?php
declare(strict_types=1);

namespace Larsnieuwenhuizen\CiHelpers\Command;

use LarsNieuwenhuizen\CiHelpers\Exception\ExecutionException;
use Larsnieuwenhuizen\CiHelpers\Exception\InvalidTagException;
use Larsnieuwenhuizen\CiHelpers\Exception\NoNewCommitsException;
use Larsnieuwenhuizen\CiHelpers\Exception\NoTagsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DefineVersionCommand extends Command
{

    const BUMP_MAJOR = 0;
    const MATCH_MAJOR = '/^(breaking)/mi';
    const BUMP_MINOR = 1;
    const MATCH_MINOR = '/^(feat|feature)/mi';
    const BUMP_PATCH = 2;
    const VERSION_PREG_MATCH = '/([vV])?([0-9]+).([0-9]+).([0-9]+)/';

    protected SymfonyStyle $io;

    public function __construct(string $name = 'version:define')
    {
        parent::__construct($name);
        $this->setDescription('Define the next version based on the last version and it\'s following commits');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        \shell_exec('git config --global user.email "no-reply@automation.user"');
        \shell_exec('git config --global user.name "Automation bot"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->io = new SymfonyStyle($input, $output);

            $tag = $this->getLastTag();
            $commits = $this->getCommitsSinceLastTag($tag);
            $bumpDecision = $this->decideBump($commits);

            if ($this->io->isVerbose()) {
                $this->io->title('Define next version');
                $this->io->info('The last tag');
                $this->io->block($tag);
                $this->io->info('Commits since last tag');
                $this->io->block($commits);
                $this->io->info('Bump decision');
                $this->io->block((string)$bumpDecision);
                $this->io->info('New version');
            }

            $this->io->text($this->nextVersion());
        } catch (ExecutionException $executionException) {
            $this->io->error($executionException->getMessage());
            exit(1);
        }
        exit(0);
    }

    protected function nextVersion(): string
    {
        $tag = $this->getLastTag();
        $commits = $this->getCommitsSinceLastTag($tag);
        $bumpDecision = $this->decideBump($commits);

        return $this->getNewVersion($bumpDecision, $tag);
    }

    private function getLastTag(): string
    {
        $tagCount = (int)\shell_exec('git --git-dir=/app/code/.git tag | wc -l');

        if ($tagCount === 0) {
            throw new NoTagsException();
        }

        $lastTag = \trim(
            \shell_exec('git --git-dir=/app/code/.git describe --tags --abbrev=0')
        );

        if (\preg_match(self::VERSION_PREG_MATCH, $lastTag) === false) {
            throw new InvalidTagException(
                'This tag is not a valid SemVer tag, it should look something like v1.0.0 or 1.0.0'
            );
        }

        return $lastTag;
    }

    private function getCommitsSinceLastTag(string $lastTag): string
    {
        $commits = \shell_exec(
            'git --git-dir=/app/code/.git log --oneline ' . $lastTag . '..HEAD --pretty=format:"%s"'
        );

        if ($commits === null) {
            throw new NoNewCommitsException('There are no commits since the last tag');
        }

        return $commits;
    }

    private function decideBump(string $commitMessages): int
    {
        if (\preg_match(self::MATCH_MAJOR, $commitMessages)) {
            return self::BUMP_MAJOR;
        }
        if (\preg_match(self::MATCH_MINOR, $commitMessages)) {
            return self::BUMP_MINOR;
        }
        return self::BUMP_PATCH;
    }

    private function getNewVersion(int $bump, string $currentVersion): string
    {
        $matches = [];
        \preg_match(self::VERSION_PREG_MATCH, $currentVersion, $matches);
        $vPrefix = false;

        if (\strtolower($matches[1]) === 'v') {
            $currentVersion = \implode(
                '.',
                [
                    $matches[2],
                    $matches[3],
                    $matches[4]
                ]
            );
            $vPrefix = true;
        }

        $versionParts = \explode('.', $currentVersion);

        if ($bump === self::BUMP_MAJOR) {
            $versionParts[0] = (int)$versionParts[0] += 1;
            $versionParts[1] = 0;
            $versionParts[2] = 0;
        }

        if ($bump === self::BUMP_MINOR) {
            $versionParts[1] = (int)$versionParts[1] += 1;
            $versionParts[2] = 0;
        }

        if ($bump === self::BUMP_PATCH) {
            $versionParts[2] = (int)$versionParts[2] += 1;
        }

        return ($vPrefix === true ? 'v' : '') .  \implode('.', $versionParts);
    }
}
