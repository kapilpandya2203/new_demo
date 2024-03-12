<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Controller\Adminhtml\Issues;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesFactory;
use Salecto\DataIntegrity\Model\Publisher\SolveIssue;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class BulkSolutionsApply extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Salecto_DataIntegrity::data_integrity';

    protected SolveIssue $solveIssue;

    protected IssueCollectionFactory $issueCollectionFactory;
    protected IntegrityIssuesFactory $issueModelFactory;

    protected IntegrityIssuesRepositoryInterface $issuesRepository;

    public function __construct(
        Context                            $context,
        SolveIssue                         $solveIssue,
        IssueCollectionFactory             $issueCollectionFactory,
        IntegrityIssuesFactory             $issueModelFactory,
        IntegrityIssuesRepositoryInterface $issuesRepository
    )
    {
        parent::__construct($context);
        $this->solveIssue = $solveIssue;
        $this->issueModelFactory = $issueModelFactory;
        $this->issueCollectionFactory = $issueCollectionFactory;
        $this->issuesRepository = $issuesRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $issueIdsPerTestArray = [];
        $solutionSelection = $this->getTestsAndSelectedSolution();

        if (!empty($solutionSelection)) {
            foreach ($solutionSelection as $testCode => $solutionCode) {
                $issueIdsPerTestArray[$testCode] = $this->getIssueIdsForTest($testCode);
            }
        }

        if (!empty($issueIdsPerTestArray)) {
            foreach ($issueIdsPerTestArray as $testCode => $issueIdArray) {
                if (isset($solutionSelection[$testCode])) {
                    $selectedSolutionCode = $solutionSelection[$testCode];
                    foreach ($issueIdArray as $issueId) {
                        $issue = $this->issuesRepository->getById($issueId);
                        if ($issue) {
                            $issue->setSelectedSolution($selectedSolutionCode);
                            $issue->setStatus('queued');
                            $this->issuesRepository->save($issue);
                            $this->solveIssue->publish($issue);
                        }
                    }
                }
            }
        }

        return $this->_redirect('*/*');
    }

    /**
     * Get tests with valid solution selected
     * @return mixed
     */
    protected function getTestsAndSelectedSolution()
    {
        $solutions = $this->getRequest()->getPostValue();

        if (isset($solutions['form_key'])) {
            unset($solutions['form_key']);
        }

        //Removing tests with skipped solutions
        foreach ($solutions as $test => $solution) {
            if ($solution === '') {
                unset($solutions[$test]);
            }
        }

        return $solutions;
    }

    /**
     * Get Ids of issues found by test
     * @param $testCode
     * @return array
     */
    protected function getIssueIdsForTest($testCode)
    {
        $issueCollection = $this->issueCollectionFactory->create();
        return $issueCollection->addFieldToFilter('test_code', $testCode)
        ->addFieldToFilter('status', 'detected')->getAllIds();
    }
}
