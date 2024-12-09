<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithWhiteSpaceSku\Solutions;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Manager;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Model\AbstractSolution;
use Salecto\DataIntegrity\Model\SolutionDependencyManager;
use Salecto\DataIntegrity\Model\SolutionInterface;

class Solution1 extends AbstractSolution implements SolutionInterface
{
    /**
     * @var string $name The name of the solution.
     */
    protected string $name = 'Solution 1';

    /**
     * @var string $code The code identifier for the solution.
     */
    protected string $code = 'solution1';

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * Constructor for the class.
     *
     * @param SolutionDependencyManager $dependencyManager
     * @param ResourceConnection        $resourceConnection
     * @param Manager                   $moduleManager
     */
    public function __construct(
        SolutionDependencyManager  $dependencyManager,
        ResourceConnection         $resourceConnection,
        Manager                    $moduleManager
    ) {
        parent::__construct($dependencyManager);
        $this->resourceConnection = $resourceConnection;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Execute the solution for resolving integrity issues related to white spaces in SKUs.
     *
     * @param IntegrityIssuesInterface $issue The integrity issue to be resolved.
     *
     * @return bool True if the solution is successfully executed and the issue is resolved, false otherwise.
     */
    public function execute(IntegrityIssuesInterface $issue): bool
    {
        $isSolved = false;
        $issueData = (array)$issue->getIssueData();
        $issueId = (int)$issue->getId();
        $oldSku = (string)$issueData['sku'];
        $tableName = (string)$issueData['table_name'];
        $idKey = (isset($issueData['entity_id'])) ? 'entity_id' : 'source_item_id';
        $idValue = $issueData[$idKey];
        $issueRepository = $this->dependencyManager->getIssueRepository();

        try {
            $connection = $this->resourceConnection->getConnection();
            $isModuleEnabled = $this->moduleManager->isEnabled('Magento_Inventory');
            $newSku = trim($oldSku);
            if (isset($issueData['entity_id']) || (isset($issueData['source_item_id']) && $isModuleEnabled)) {
                $connection->update(
                    $tableName,
                    ['sku' => $newSku],
                    [$idKey . ' = ?' => $idValue]
                );
            }
            $issueRepository->delete($issue);
            $isSolved = true;
        } catch (Exception $exception) {
            $this->logError($exception, $issueId);
        }
        return $isSolved;
    }
}
