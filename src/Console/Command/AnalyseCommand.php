<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AnalyseCommand extends Command
{
    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('analyse');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @todo process :)
        // @todo build family tree of available classes, use bare php-parser?
        // @todo run phpstan and get collected data in json

        return Command::SUCCESS;
    }
}
