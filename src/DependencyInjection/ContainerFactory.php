<?php

declare(strict_types=1);

namespace TomasVotruba\Finalize\DependencyInjection;

use Illuminate\Container\Container;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Finalize\Command\ClassTreeCommand;

final class ContainerFactory
{
    /**
     * @api used in bin and tests
     */
    public function create(): Container
    {
        $container = new Container();

        // console
        $container->singleton(
            SymfonyStyle::class,
            static fn (): SymfonyStyle => new SymfonyStyle(new ArrayInput([]), new ConsoleOutput())
        );

        $container->singleton(Application::class, function (Container $container): Application {
            $application = new Application();

            $vendorCommand = $container->make(ClassTreeCommand::class);
            $application->add($vendorCommand);

            // hide basic commands to make output clear
            $this->hideDefaultCommands($application);

            return $application;
        });

        // parser
        $container->singleton(Parser::class, static function (): Parser {
            $phpParserFactory = new ParserFactory();
            return $phpParserFactory->create(ParserFactory::PREFER_PHP7);
        });

        return $container;
    }

    public function hideDefaultCommands(Application $application): void
    {
        $application->get('list')->setHidden();
        $application->get('help')->setHidden();
        $application->get('completion')->setHidden();
    }
}
