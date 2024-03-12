<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Cron;

use Salecto\DataIntegrity\Model\IntegrityTests;

/**
 * detects data integrity issues
 */
class DetectIssues
{
    protected IntegrityTests $tests;

    public function __construct(
        IntegrityTests $tests
    )
    {
        $this->tests = $tests;
    }

    /**
     * detect issues
     *
     */
    public function execute()
    {
        $this->tests->runAll();
    }
}
