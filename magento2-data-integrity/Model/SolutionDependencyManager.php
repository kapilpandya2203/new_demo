<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use Psr\Log\LoggerInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;

class SolutionDependencyManager
{
    protected ReadmeReader $readmeReader;
    protected IntegrityIssuesRepositoryInterface $issuesRepository;
    protected LoggerInterface $logger;

    public function __construct(
        ReadmeReader                       $readmeReader,
        IntegrityIssuesRepositoryInterface $issuesRepository,
        LoggerInterface                    $logger
    )
    {
        $this->readmeReader = $readmeReader;
        $this->issuesRepository = $issuesRepository;
        $this->logger = $logger;
    }

    /**
     * @return ReadmeReader
     */
    public function getReadmeReader(): ReadmeReader
    {
        return $this->readmeReader;
    }

    /**
     * @return IntegrityIssuesRepositoryInterface
     */
    public function getIssueRepository(): IntegrityIssuesRepositoryInterface
    {
        return $this->issuesRepository;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
