<?php

namespace Salecto\DataIntegrity\Ui\Component\Form\Fieldset;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Salecto\DataIntegrity\Model\IntegrityTests;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class BulkSolutions extends SolutionsFieldset
{

    public function __construct(
        ContextInterface       $context,
        FieldFactory           $fieldFactory,
        IssueCollectionFactory $issueCollectionFactory,
        IntegrityTests         $integrityTests,
        array                  $components = [],
        array                  $data = []
    )
    {
        parent::__construct($context, $fieldFactory, $issueCollectionFactory, $integrityTests, $components, $data);
        $this->enableSkipOption();
        $this->showIssueCount();
        $this->disableRestrictTests();
    }
}
