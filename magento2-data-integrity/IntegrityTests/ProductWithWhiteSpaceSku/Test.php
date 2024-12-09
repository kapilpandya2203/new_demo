<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithWhiteSpaceSku;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\StoreManagerInterface;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\TestDependencyManager;
use Salecto\DataIntegrity\Model\TestInterface;

class Test extends AbstractTest implements TestInterface
{
    /**
     * Table names
     */
    private const CATALOG_PRODUCT_TABLE = 'catalog_product_entity';
    private const INVENTORY_SOURCE_TABLE = 'inventory_source_item';

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * Constructor for the ProductWithWhiteSpaceSku Test class.
     *
     * @param array                 $solutions
     * @param TestDependencyManager $dependencyManager
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection    $resourceConnection
     * @param Manager               $moduleManager
     */
    public function __construct(
        array                    $solutions,
        TestDependencyManager    $dependencyManager,
        StoreManagerInterface    $storeManager,
        ResourceConnection       $resourceConnection,
        Manager                  $moduleManager
    ) {
        parent::__construct($solutions, $dependencyManager);
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Configure command details.
     *
     * Sets the name, code, and description template for the command.
     */
    public function configure()
    {
        $this->setName('Product with whitespace sku');
        $this->setCode('product-with-whitespace-sku');
        $this->setDescriptionTemplate("The product with SKU {sku} has a white space at beginning or ending in the {table_name} table.");
    }

    /**
     * Run the ProductWithWhiteSpaceSku test.
     *
     * Checks and adds issues for products with white space at beginning or end of their sku in specified tables.
     *
     * @throws Exception If an error occurs during the process.
     */
    public function run()
    {
        try {
            $catalogTables = [self::CATALOG_PRODUCT_TABLE];
            $connection = $this->resourceConnection->getConnection();

            if ($this->moduleManager->isEnabled('Magento_Inventory')) {
                $catalogTables[] = self::INVENTORY_SOURCE_TABLE;
            }
            foreach ($catalogTables as $table) {
                $tableName = $this->resourceConnection->getTableName($table);
                $query = $connection->select()
                    ->from($tableName)
                    ->where("sku LIKE ' %' OR sku LIKE '% '");
                $result = $connection->fetchAll($query);

                if (!empty($result)) {
                    foreach ($result as $data) {
                        $idKey = isset($data['entity_id']) ? 'entity_id' : 'source_item_id';
                        $this->addIssue(
                            ['table_name' => $tableName,
                                'sku' => $data['sku'],
                                $idKey => $data[$idKey]
                            ],
                            ['table_name' => $tableName,
                                'sku' => $data['sku']
                            ]
                        );
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logError($exception, $this->getCode());
        }
    }
}
