<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Cron;

use DateInterval;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesRepository;

/**
 * cleans up issues that have not been processed for long
 */
class CleanupOldIssues
{
    protected IntegrityIssuesRepository $issueRepository;
    protected TimezoneInterface $timezone;

    public function __construct(
        IntegrityIssuesRepository $issuesRepository,
        TimezoneInterface         $timezone
    )
    {
        $this->issueRepository = $issuesRepository;
        $this->timezone = $timezone;
    }

    public function execute()
    {
        $date = $this->timezone->date()->add(new DateInterval('P5D'));
        $this->issueRepository->deleteIssuesOlderThanDate($date);
    }
}
