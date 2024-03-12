<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use InvalidArgumentException;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesExtensionInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;

class IntegrityIssues extends AbstractExtensibleModel implements IntegrityIssuesInterface
{
    private const ISSUE_ID = 'issue_id';
    private const TEST_NAME = 'test_name';
    private const TEST_CODE = 'test_code';
    private const DESCRIPTION = 'description';
    private const ISSUE_DATA = 'issue_data';
    private const SELECTED_SOLUTION = 'selected_solution';
    private const STATUS = 'status';
    private const CHECKSUM = 'checksum';
    private const CREATED_AT = 'created_at';
    private const UPDATED_AT = 'updated_at';
    protected SerializerInterface $serializer;

    public function __construct(
        Context                    $context,
        Registry                   $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory      $customAttributeFactory,
        SerializerInterface        $serializer,
        AbstractResource           $resource = null,
        AbstractDb                 $resourceCollection = null,
        array                      $data = []
    )
    {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\IntegrityIssues::class);
        $this->setIdFieldName(self::ISSUE_ID);
    }

    public function setTestName(string $testName): self
    {
        return $this->setData(self::TEST_NAME, $testName);
    }

    public function getTestName(): string
    {
        return $this->getData(self::TEST_NAME);
    }

    public function setTestCode(string $testCode): self
    {
        return $this->setData(self::TEST_CODE, $testCode);
    }

    public function getTestCode(): string
    {
        return $this->getData(self::TEST_CODE);
    }

    public function setDescription(string $description): self
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getDescription(): string
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setSelectedSolution(string $solutionCode): self
    {
        return $this->setData(self::SELECTED_SOLUTION, $solutionCode);
    }

    public function getSelectedSolution(): string
    {
        return $this->getData(self::SELECTED_SOLUTION);
    }

    public function setStatus(string $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getStatus(): string
    {
        return $this->getData(self::STATUS);
    }

    public function setCreatedAt($createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setUpdatedAt($updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setIssueData($issueData): self
    {
        $issueData = $this->serializer->serialize($issueData);
        return $this->setData(self::ISSUE_DATA, $issueData);
    }

    public function getIssueData()
    {
        $issueData = $this->getData(self::ISSUE_DATA);
        try {
            $issueData = $this->serializer->unserialize($issueData);
        } catch (InvalidArgumentException $e) {
            $issueData = null;
        }
        return $issueData;
    }

    public function setChecksum(string $checksum): self
    {
        return $this->setData(self::CHECKSUM, $checksum);
    }

    public function getChecksum(): string
    {
        return $this->getData(self::CHECKSUM);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): IntegrityIssuesExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(IntegrityIssuesExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
