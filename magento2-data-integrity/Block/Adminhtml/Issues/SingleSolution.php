<?php

namespace Salecto\DataIntegrity\Block\Adminhtml\Issues;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;

class SingleSolution extends Template
{
    protected Http $request;
    protected IntegrityIssuesRepositoryInterface $issuesRepository;

    public function __construct(
        Context $context,
        Http                                             $request,
        IntegrityIssuesRepositoryInterface               $issuesRepository,
        array                                            $data = []
    )
    {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->issuesRepository = $issuesRepository;
    }

    /**
     * get description of the current issue
     * @return string
     */
    public function getCurrentIssueDescription()
    {
        $description = '';
        $issueCode = $this->request->getParam('test_code');
        if (!empty($issueCode)) {
            $issue = $this->issuesRepository->getByCode($issueCode);
            if ($issue->getId()) {
                $description = $issue->getDescription();
            }
        }

        return $description;
    }
}
