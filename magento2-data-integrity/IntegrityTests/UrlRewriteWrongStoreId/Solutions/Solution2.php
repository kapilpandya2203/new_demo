<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId\Solutions;

use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId\Classes\ParentClass;
use Salecto\DataIntegrity\Model\SolutionInterface;

class Solution2 extends ParentClass implements SolutionInterface
{
    protected string $name = 'Solution 2';

    protected string $code = 'solution2';

    /**
     * Checks if the url rewrite exists on the right store level. If it does not exist then it creates it and then
     * deletes the url rewrite on the wrong store level.
     * @param IntegrityIssuesInterface $issue
     * @return bool
     */
    public function execute(IntegrityIssuesInterface $issue): bool
    {
        return $this->solveIssue($issue, true);
    }
}
