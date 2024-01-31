<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Finalize\FileSystem\JsonFileSystem;
use TomasVotruba\Finalize\ParentClassResolver;

final class ClassTreeCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ParentClassResolver $parentClassResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('class-tree');
        $this->setDescription('Generate class family tree for provided project');
        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Paths to analyze');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument('paths');

        $phpFileInfos = $this->findPhpFileInfos($paths);
        $this->symfonyStyle->progressStart(count($phpFileInfos));

        $progressClosure = function () {
            $this->symfonyStyle->progressAdvance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);

        $projectHash = Strings::webalize(implode('|', $paths));

        JsonFileSystem::writeCacheFile($projectHash, [
            'parent_class_names' => $parentClassNames,
        ]);

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->note(sprintf('Found %d parent classes', count($parentClassNames)));
        $this->symfonyStyle->success('Done');

        return Command::SUCCESS;
    }

    /**
     * @param string[] $paths
     * @return SplFileInfo[]
     */
    private function findPhpFileInfos(array $paths): array
    {
        $phpFinder = Finder::create()
            ->files()
            ->in($paths)
            ->name('*.php');

        return iterator_to_array($phpFinder);
    }
}
