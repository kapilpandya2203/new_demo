<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesFactory as IssuesModelFactory;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssuesCollectionFactory;

class TestDependencyManager
{
    protected IntegrityIssuesRepositoryInterface $issuesRepository;
    protected IssuesModelFactory $issueModelFactory;
    protected LoggerInterface $logger;
    protected IssuesCollectionFactory $issuesCollectionFactory;
    protected SerializerInterface $serializer;
    protected ReadmeReader $readmeReader;

    public function __construct(
        IntegrityIssuesRepositoryInterface $issuesRepository,
        IssuesModelFactory                 $issueModelFactory,
        IssuesCollectionFactory            $issueCollectionFactory,
        ReadmeReader                       $readmeReader,
        SerializerInterface                $serializer,
        LoggerInterface                    $logger
    )
    {
        $this->issuesRepository = $issuesRepository;
        $this->issueModelFactory = $issueModelFactory;
        $this->issuesCollectionFactory = $issueCollectionFactory;
        $this->readmeReader = $readmeReader;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @return IntegrityIssuesRepositoryInterface
     */
    public function getIssueRepository(): IntegrityIssuesRepositoryInterface
    {
        return $this->issuesRepository;
    }

    /**
     * @return IssuesModelFactory
     */
    public function getIssueModelFactory(): IntegrityIssuesFactory
    {
        return $this->issueModelFactory;
    }

    /**
     * @return IssuesCollectionFactory
     */
    public function getIssueCollectionFactory(): IssuesCollectionFactory
    {
        return $this->issuesCollectionFactory;
    }

    /**
     * @return ReadmeReader
     */
    public function getReadmeReader(): ReadmeReader
    {
        return $this->readmeReader;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
