<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Finalize\EntityClassResolver;
use TomasVotruba\Finalize\FileSystem\PhpFilesFinder;
use TomasVotruba\Finalize\ParentClassResolver;

final class FinalizeCommand extends Command
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
        $this->setName('finalize');

        $this->setDescription('Generate class family tree and make all safe classes final');

        $this->addArgument('paths', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Directories to finalize');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $paths = (array) $input->getArgument('paths');

        $phpFileInfos = PhpFilesFinder::findPhpFileInfos($paths);

        $this->symfonyStyle->title('1. Detecting parent classes');

        // double to count for both parent and entity resolver
        $this->symfonyStyle->progressStart(2 * count($phpFileInfos));

        $progressClosure = function () {
            $this->symfonyStyle->progressAdvance();
        };

        $parentClassNames = $this->parentClassResolver->resolve($phpFileInfos, $progressClosure);
        $entityClassNames = $this->entityClassResolver->resolve($phpFileInfos, $progressClosure);

        $this->symfonyStyle->newLine();
        $this->symfonyStyle->note(sprintf('Found %d parent classes', count($parentClassNames)));
        $this->symfonyStyle->note(sprintf('Found %d entity classes', count($entityClassNames)));

        $this->symfonyStyle->title('2. Finalizing safe classes');

        dump($parentClassNames);
        dump($entityClassNames);
        die;

        $this->symfonyStyle->success('Done');

        return Command::SUCCESS;
    }
}
