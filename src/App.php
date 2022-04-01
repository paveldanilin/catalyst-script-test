<?php

namespace Pada\CatalystScriptTest;

use Pada\CatalystScriptTest\Console\Console;
use Pada\CatalystScriptTest\Console\ConsoleInterface;
use Pada\CatalystScriptTest\Console\InputInterface;
use Pada\CatalystScriptTest\Console\OptionDefinition;
use Pada\CatalystScriptTest\Console\OutputInterface;

final class App
{
    private ConsoleInterface $console;

    public function __construct()
    {
        $this->console = (new Console('user_upload.php'))
            ->addDefinition(new OptionDefinition(
                null,
                'file',
                OptionDefinition::VALUE_REQUIRED,
                'This is the name of the CSV file to be parsed',
                fn(OutputInterface $output, InputInterface $input) => $this->uploadCommand($output, $input)
            ))
            ->addDefinition(new OptionDefinition(
                null,
                'create_table',
                OptionDefinition::VALUE_NONE,
                'Will cause the MySQL users table to be built',
                fn(OutputInterface $output, InputInterface $input) => $this->createTableCommand($output, $input)
            ))
            ->addDefinition(new OptionDefinition(
                null, 'dry_run', OptionDefinition::VALUE_NONE, 'Database not will be altered'
            ))
            ->addDefinition(new OptionDefinition(
                'u', null, OptionDefinition::VALUE_REQUIRED, 'MySQL username'
            ))
            ->addDefinition(new OptionDefinition(
                'p', null, OptionDefinition::VALUE_REQUIRED, 'MySQL password'
            ))
            ->addDefinition(new OptionDefinition(
                'h', null, OptionDefinition::VALUE_REQUIRED, 'MySQL hostname'
            ));
    }

    public function run(array $argv): void
    {
        $this->console->run($argv);
    }

    private function uploadCommand(OutputInterface $output, InputInterface $input): void
    {
        $this->checkRequiredDBOptions($output, $input);
        $output->writeln('UPLOAD!');
    }

    private function createTableCommand(OutputInterface $output, InputInterface $input): void
    {
        $this->checkRequiredDBOptions($output, $input);
        $output->writeln('CREATE!');
    }

    private function checkRequiredDBOptions(OutputInterface $output, InputInterface $input): void
    {
        if (!$input->hasOption('u')) {
            $output->writeln('[ERROR] Required [-u] option', ['color' => 'red']);
            return;
        }
        if (!$input->hasOption('p')) {
            $output->writeln('[ERROR] Required [-p] option', ['color' => 'red']);
            return;
        }
        if (!$input->hasOption('h')) {
            $output->writeln('[ERROR] Required [-h] option', ['color' => 'red']);
            return;
        }
    }
}
