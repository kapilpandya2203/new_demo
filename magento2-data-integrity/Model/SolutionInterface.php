<?php

namespace Salecto\DataIntegrity\Model;

use Exception;
use Magento\Framework\Phrase;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;

interface SolutionInterface
{
    public function execute(IntegrityIssuesInterface $issue): bool;

    public function getName(): string;

    public function getCode(): string;

    public function getReadmeInHtml(): string;

    public function logError(Exception $exception, ?int $issueId, ?Phrase $message = null): void;
}
