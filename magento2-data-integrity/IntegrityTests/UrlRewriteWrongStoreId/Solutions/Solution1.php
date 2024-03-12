<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId\Solutions;

use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId\Classes\ParentClass;
use Salecto\DataIntegrity\Model\SolutionInterface;

class Solution1 extends ParentClass implements SolutionInterface
{
    protected string $name = 'Solution 1';

    protected string $code = 'solution1';

    /**
     * Deletes the URL rewrite on the wrong store level
     * @param IntegrityIssuesInterface $issue
     * @return void
     */
    public function execute(IntegrityIssuesInterface $issue): bool
    {
        return $this->solveIssue($issue);
    }
}
