<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Controller\Adminhtml\Issues;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesFactory;
use Salecto\DataIntegrity\Model\Publisher\SolveIssue;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class SingleSolutionApply extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Salecto_DataIntegrity::data_integrity';

    protected SolveIssue $solveIssue;
    protected Http $request;
    protected IssueCollectionFactory $issueCollectionFactory;
    protected IntegrityIssuesFactory $issueModelFactory;

    protected IntegrityIssuesRepositoryInterface $issuesRepository;

    public function __construct(
        Context                            $context,
        SolveIssue                         $solveIssue,
        Http                               $request,
        IssueCollectionFactory             $issueCollectionFactory,
        IntegrityIssuesFactory             $issueModelFactory,
        IntegrityIssuesRepositoryInterface $issuesRepository
    )
    {
        parent::__construct($context);
        $this->request = $request;
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
        $solutionSelection = $this->getTestsAndSelectedSolution();
        $selectedSolutionCode = implode(' ', array_values($solutionSelection));
        if (!empty($solutionSelection)) {
            $getCurrentId = $this->request->getParams();
            $issueId = $getCurrentId['general']['issue_id'];
            $issue = $this->issuesRepository->getById($issueId);
            if ($issue) {
                $issue->setSelectedSolution($selectedSolutionCode);
                $issue->setStatus('queued');
                $this->issuesRepository->save($issue);
                $this->solveIssue->publish($issue);
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
            unset($solutions['general']);
        }

        return $solutions;
    }
}
