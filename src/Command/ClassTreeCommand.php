<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Finalize\EntityClassResolver;
use TomasVotruba\Finalize\FileSystem\JsonFileSystem;
use TomasVotruba\Finalize\FileSystem\PhpFilesFinder;
use TomasVotruba\Finalize\ParentClassResolver;

final class ClassTreeCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
        private readonly ParentClassResolver $parentClassResolver,
        private readonly EntityClassResolver $entityClassResolver,
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

        $phpFileInfos = PhpFilesFinder::findPhpFileInfos($paths);

        // double to count for both parent and entity resolver
        $this->symfonyStyle->progressStart(2 * count($phpFileInfos));

        $progressClosure = function () {
            $this->symfonyStyle->progressAdvance();
        };

        $projectHash = Strings::webalize(getcwd());

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($phpFileInfos, $progressClosure);

        JsonFileSystem::writeCacheFile($projectHash, [
            'parent_class_names' => $parentClassNames,
            'entity_class_names' => $entityClassNames,
        ]);

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->note(sprintf('Found %d parent classes', count($parentClassNames)));
        $this->symfonyStyle->note(sprintf('Found %d entity classes', count($entityClassNames)));
        $this->symfonyStyle->success('Done');

        return Command::SUCCESS;
    }
}
