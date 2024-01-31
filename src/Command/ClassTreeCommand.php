<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Command;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Finalize\FileSystem\JsonFileSystem;
use TomasVotruba\Finalize\NodeVisitor\ParentClassNameCollectingNodeVisitor;

final class ClassTreeCommand extends Command
{
    private Parser $parser;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();

        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
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

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());

        $parentClassNameCollectingNodeVisitor = new ParentClassNameCollectingNodeVisitor();
        $nodeTraverser->addVisitor($parentClassNameCollectingNodeVisitor);

        $this->traverseFileInfos($phpFileInfos, $nodeTraverser);

        $parentClassNames = $parentClassNameCollectingNodeVisitor->getParentClassNames();

        JsonFileSystem::writeCacheFile([
            'parent_class_names' => $parentClassNames,
        ]);

        $this->symfonyStyle->note(sprintf('Found %d parent classes', count($parentClassNames)));
        $this->symfonyStyle->success('Done');

        return Command::SUCCESS;
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function traverseFileInfos(array $phpFileInfos, NodeTraverser $nodeTraverser): void
    {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->parser->parse($phpFileInfo->getContents());
            if (! is_array($stmts)) {
                continue;
            }

            $nodeTraverser->traverse($stmts);
            $this->symfonyStyle->progressAdvance();
        }
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
