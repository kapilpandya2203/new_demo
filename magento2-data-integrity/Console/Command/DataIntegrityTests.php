<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Console\Command;

use Salecto\DataIntegrity\Model\IntegrityTests;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DataIntegrityTests extends Command
{
    protected IntegrityTests $tests;

    public function __construct(
        IntegrityTests $tests,
        string         $name = null
    )
    {
        $this->tests = $tests;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('data-integrity:issues:detect');
        $this->setDescription('Runs data integrity tests to detect issues');
    }

    /**
     * Runs all tests
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        $output->writeln('<info>' . __('Running Data Integrity Tests...') . '</info>');
        $this->tests->runAll();
        $output->writeln('<info>' . __('Completed Data Integrity Tests.') . '</info>');

        return $exitCode;
    }
}
