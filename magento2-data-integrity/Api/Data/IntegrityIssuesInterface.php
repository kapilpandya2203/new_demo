<?php

namespace Salecto\DataIntegrity\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IntegrityIssuesInterface extends ExtensibleDataInterface
{
    /**
     * @param mixed $value
     * @return self
     */
    public function setId($value);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $testName
     * @return self
     */
    public function setTestName(string $testName): self;

    /**
     * @return string
     */
    public function getTestName(): string;

    /**
     * @param string $testCode
     * @return self
     */
    public function setTestCode(string $testCode): self;

    /**
     * @return string
     */
    public function getTestCode(): string;

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param $issueData
     * @return self
     */
    public function setIssueData($issueData): self;

    /**
     * @return mixed
     */
    public function getIssueData();

    /**
     * @param string $solutionCode
     * @return self
     */
    public function setSelectedSolution(string $solutionCode): self;

    /**
     * @return string
     */
    public function getSelectedSolution(): string;

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $checksum
     * @return self
     */
    public function setChecksum(string $checksum): self;

    /**
     * @return string
     */
    public function getChecksum(): string;

    /**
     * Get created at
     *
     * @param $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt): self;

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get created at
     *
     * @param $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt): self;

    /**
     * Get created at
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Salecto\DataIntegrity\Api\Data\IntegrityIssuesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Salecto\DataIntegrity\Api\Data\IntegrityIssuesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Salecto\DataIntegrity\Api\Data\IntegrityIssuesExtensionInterface $extensionAttributes
    );
}
