<?php

namespace Salecto\DataIntegrity\Ui\Component\Form\Fieldset;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\IntegrityTests;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues\CollectionFactory as IssueCollectionFactory;

class SolutionsFieldset extends Fieldset
{
    protected FieldFactory $fieldFactory;

    protected IssueCollectionFactory $issueCollectionFactory;

    protected $activeTests;

    protected IntegrityTests $integrityTests;

    private bool $enableSkipOption = false;

    private bool $enableRestrictTests = true;

    private bool $showIssueCount = false;

    /**
     * Empty array signifies all test codes are allowed
     * @var array
     */
    protected array $allowedTestCodes = [];

    public function __construct(
        ContextInterface       $context,
        FieldFactory           $fieldFactory,
        IssueCollectionFactory $issueCollectionFactory,
        IntegrityTests         $integrityTests,
        array                  $components = [],
        array                  $data = []
    )
    {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->issueCollectionFactory = $issueCollectionFactory;
        $this->integrityTests = $integrityTests;
    }

    public function disableRestrictTests()
    {
        $this->enableRestrictTests = false;
    }

    /**
     * Adds a test code to the list of allowed test codes.
     * This is used only if restrict tests is enabled.
     * @param string $testCode
     * @return void
     */
    public function addAllowedTestCode(string $testCode)
    {
        $this->allowedTestCodes[] = $testCode;
    }

    /**
     * Adds an option to skip applying a solution for a test
     * @return void
     */
    public function enableSkipOption()
    {
        $this->enableSkipOption = true;
    }

    /**
     * Shows the issue count below the solutions dropdown for a test
     * @return void
     */
    public function showIssueCount()
    {
        $this->showIssueCount = true;
    }

    /**
     * @param string $testCode
     * @return bool
     */
    public function isTestCodeAllowed(string $testCode)
    {
        return !$this->enableRestrictTests || in_array($testCode, $this->allowedTestCodes);
    }

    /**
     * @return array
     */
    private function getCurrentTests()
    {
        if ($this->activeTests === null) {
            $this->activeTests = [];

            $query = $this->issueCollectionFactory->create()
                ->addFieldToSelect('test_code')
                ->addFieldToFilter('status', 'detected')
                ->getSelect()
                ->group('test_code')->query();

            $testCodesArray = $query->fetchAll();

            foreach ($testCodesArray as $item) {
                $activeTest = $this->integrityTests->getTestByCode($item['test_code']);
                if ($activeTest instanceof AbstractTest) {
                    $this->activeTests[] = $activeTest;
                }
            }
        }

        return $this->activeTests;
    }

    public function getFields()
    {
        $fields = [];
        $currentTests = $this->getCurrentTests();

        foreach ($currentTests as $test) {
            /* @var AbstractTest $test */

            if (!$this->isTestCodeAllowed($test->getCode())) {
                continue;
            }

            $additionalInfo = '';

            if ($this->showIssueCount) {
                $detectedIssues = $this->issueCollectionFactory->create()
                    ->addFieldToSelect('issue_id')
                    ->addFieldToFilter('test_code', $test->getCode())
                    ->addFieldToFilter('status', 'detected')
                    ->count();
                $additionalInfo = __('Issues Detected: ') . $detectedIssues;
            }

            $fields[] = [
                'label' => $test->getName(),
                'options' => $this->getOptions($test),
                'formElement' => 'select',
                'name' => $test->getCode(),
                'additionalInfo' => $additionalInfo
            ];
        }

        return $fields;
    }

    protected function getOptions(AbstractTest $test)
    {
        $options = [];

        if ($this->enableSkipOption) {
            $options = [
                [
                    'label' => __('Skip'),
                    'value' => ''
                ],
            ];
        }

        $solutions = $test->getSolutions();
        foreach ($solutions as $solution) {
            $options[] = [
                'label' => $solution->getName(),
                'value' => $solution->getCode()
            ];
        }

        return $options;
    }

    public function getChildComponents()
    {
        $fields = $this->getFields();
        foreach ($fields as $key => $field) {
            $fieldInstance = $this->fieldFactory->create();
            $name = $field['name'];
            $fieldInstance->setData(
                [
                    'config' => $field,
                    'name' => $name
                ]
            );
            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }
        return parent::getChildComponents();
    }
}
