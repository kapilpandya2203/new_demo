<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use DateTime;
use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Psr\Log\LoggerInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesFactory as IssuesFactory;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues as IssuesResource;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class IntegrityIssuesRepository implements IntegrityIssuesRepositoryInterface
{
    protected IntegrityIssuesFactory $issuesFactory;
    protected IssuesResource $issuesResource;
    protected IssueCollectionFactory $issueCollectionFactory;
    protected DateTime $dateTime;
    protected LoggerInterface $logger;

    public function __construct(
        IssuesFactory          $issuesFactory,
        IssueCollectionFactory $issueCollectionFactory,
        IssuesResource         $issuesResource,
        DateTime               $dateTime,
        LoggerInterface        $logger
    )
    {
        $this->issuesFactory = $issuesFactory;
        $this->issuesResource = $issuesResource;
        $this->issueCollectionFactory = $issueCollectionFactory;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @param int $issueId
     * @return IntegrityIssuesInterface
     */
    public function getById($issueId)
    {
        $issue = $this->issuesFactory->create();
        $this->issuesResource->load($issue, $issueId);
        return $issue;
    }

    /**
     * @param string $issueCode
     * @return IntegrityIssuesInterface
     */
    public function getByCode($issueCode)
    {
        $issue = $this->issuesFactory->create();
        $this->issuesResource->load($issue, $issueCode, 'test_code');
        return $issue;
    }

    /**
     * @param IntegrityIssuesInterface $issue
     * @return IntegrityIssuesInterface
     * @throws AlreadyExistsException
     */
    public function save(IntegrityIssuesInterface $issue)
    {
        $this->issuesResource->save($issue);
        return $issue;
    }

    /**
     * @param IntegrityIssuesInterface $issue
     * @return bool
     * @throws Exception
     */
    public function delete(IntegrityIssuesInterface $issue)
    {
        $this->issuesResource->delete($issue);
        return true;
    }

    /**
     * @param int $issueId
     * @return bool
     * @throws Exception
     */
    public function deleteById($issueId)
    {
        $issue = $this->getById($issueId);
        return $this->delete($issue);
    }

    /**
     * Deletes issues older than $date
     * @param DateTime $date
     * @return bool
     */
    public function deleteIssuesOlderThanDate(DateTime $date)
    {
        $areIssuesDelete = false;
        try {
            $issueCollection = $this->issueCollectionFactory->create();
            $issueCollection->addFieldToFilter('updated_at', ['gt' => $date->format('Y-m-d')]);
            $issueCollection->walk('delete');
            $areIssuesDelete = true;
        } catch (Exception $e) {
            $this->logger->error(__('Could not delete issues: %1', $e->getMessage()));
        }

        return $areIssuesDelete;
    }
}
