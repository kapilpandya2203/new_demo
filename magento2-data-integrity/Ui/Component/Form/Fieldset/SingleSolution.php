<?php

namespace Salecto\DataIntegrity\Ui\Component\Form\Fieldset;

use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Salecto\DataIntegrity\Model\IntegrityTests;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class SingleSolution extends SolutionsFieldset
{
    protected Http $request;

    public function __construct(
        ContextInterface       $context,
        FieldFactory           $fieldFactory,
        IssueCollectionFactory $issueCollectionFactory,
        IntegrityTests         $integrityTests,
        Http                   $request,
        array                  $components = [],
        array                  $data = []
    )
    {
        parent::__construct($context, $fieldFactory, $issueCollectionFactory, $integrityTests, $components, $data);
        $this->request = $request;
        $testCode = $this->request->getParam('test_code');
        if (!empty($testCode)) {
            $this->addAllowedTestCode($testCode);
        }
    }
}
