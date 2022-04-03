<?php

namespace Pada\CatalystScriptTest;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Pada\CatalystScriptTest\Console\Console;
use Pada\CatalystScriptTest\Console\ConsoleInterface;
use Pada\CatalystScriptTest\Console\InputInterface;
use Pada\CatalystScriptTest\Console\OptionDefinition;
use Pada\CatalystScriptTest\Console\OutputInterface;
use Psr\Log\LoggerInterface;

final class App
{
    private ConsoleInterface $console;
    private UserUploadServiceInterface $userUploadService;
    private ConfigInterface $config;

    public function __construct(UserUploadServiceInterface $userUploadService, ConfigInterface $config)
    {
        $this->config = $config;
        $this->userUploadService = $userUploadService;
        $this->console = (new Console('user_upload.php'))
            ->addDefinition(new OptionDefinition(
                null,
                'file',
                OptionDefinition::VALUE_REQUIRED,
                'This is the name of the CSV file to be parsed. ' .
                'The following options are required: [-u=user] [-p=password] [-d=database] [-h=hostname|hostname:port]',
                fn(OutputInterface $output, InputInterface $input) => $this->uploadUsersCommand($output, $input),
                'CSV filename'
            ))
            ->addDefinition(new OptionDefinition(
                null,
                'create_table',
                OptionDefinition::VALUE_NONE,
                'Will cause the MySQL users table to be built. ' .
                'The following options are required: [-u=user] [-p=password] [-d=database] [-h=hostname|hostname:port]',
                fn(OutputInterface $output, InputInterface $input) => $this->createTableCommand($output, $input),
            ))
            ->addDefinition(new OptionDefinition(
                null,
                'dry_run',
                OptionDefinition::VALUE_NONE,
                'Database not will be altered'
            ))
            ->addDefinition(new OptionDefinition(
                'u',
                null,
                OptionDefinition::VALUE_REQUIRED,
                'MySQL username',
                null,
                'username'
            ))
            ->addDefinition(new OptionDefinition(
                'p',
                null,
                OptionDefinition::VALUE_REQUIRED,
                'MySQL password',
                null,
                'password'
            ))
            ->addDefinition(new OptionDefinition(
                'h',
                null,
                OptionDefinition::VALUE_REQUIRED,
                'MySQL hostname',
                null,
                'hostname|hostname:port'
            ))
            ->addDefinition(new OptionDefinition(
                'd',
                null,
                OptionDefinition::VALUE_REQUIRED,
                'MySQL database',
                null,
                'dbname'
            ))
            ->addDefinition(new OptionDefinition(
                null,
                'log',
                OptionDefinition::VALUE_NONE,
                'Write log'));
    }

    public function run(array $argv): void
    {
        $this->console->run($argv);
    }

    private function uploadUsersCommand(OutputInterface $output, InputInterface $input): void
    {
        $this->checkRequiredDBOptions($output, $input);

        if ($input->hasOption('log')) {
            $this->userUploadService->setLogger($this->createLogger());
        }

        $result = $this->userUploadService->upload(
            $input->getOption('file')->getValue(),
            [
                'driver' => 'mysql',
                'dbname' => $input->getOption('d')->getValue(),
                'user' => $input->getOption('u')->getValue(),
                'password' => $input->getOption('p')->getValue(),
                'host' => $input->getOption('h')->getValue()
            ],
            $input->hasOption('dry_run'),
        );

        foreach ($result->getErrors() as $error) {
            $output->writeln($error, ['color' => 'red']);
        }
        $output->writeln('* Processed:' . $result->getProcessed());
        $output->writeln('* Inserted: ' . $result->getInserted());
        $output->writeln('* Skipped:  ' . $result->getSkipped());
    }

    private function createTableCommand(OutputInterface $output, InputInterface $input): void
    {
        $this->checkRequiredDBOptions($output, $input);

        if ($input->hasOption('log')) {
            $this->userUploadService->setLogger($this->createLogger());
        }

        $this->userUploadService->createTable(            [
            'driver' => 'mysql',
            'dbname' => $input->getOption('d')->getValue(),
            'user' => $input->getOption('u')->getValue(),
            'password' => $input->getOption('p')->getValue(),
            'host' => $input->getOption('h')->getValue()
        ]);

        $output->writeln('Table has been created', ['color' => 'green']);
    }

    private function checkRequiredDBOptions(OutputInterface $output, InputInterface $input): void
    {
        $this->requireOptionOrDie('u', $input);
        $this->requireOptionOrDie('p', $input);
        $this->requireOptionOrDie('h', $input);
        $this->requireOptionOrDie('d', $input);
    }

    private function requireOptionOrDie(string $option, InputInterface $input): void
    {
        if (!$input->hasOption($option)) {
            throw new \RuntimeException('Option [-' . $option . '] is required');
        }
    }

    private function createLogger(): LoggerInterface
    {
        return (new Logger('uploader'))
            ->pushHandler(new RotatingFileHandler(
                $this->config->getLogDir() . $this->config->getLogFilename(),
                30,
                Logger::DEBUG))
            ->pushProcessor(new PsrLogMessageProcessor());
    }
}
