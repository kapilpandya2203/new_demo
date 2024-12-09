<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath;

use Exception;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\Store;
use Salecto\DataIntegrity\IntegrityTests\Classes\StoresUtil;
use Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Classes\UrlPathManager;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\TestDependencyManager;
use Salecto\DataIntegrity\Model\TestInterface;


class Test extends AbstractTest implements TestInterface
{
    protected ProductCollectionFactory $productCollectionFactory;
    protected StoresUtil $storesUtil;
    protected UrlPathManager $urlPathManager;

    public function __construct(
        array                    $solutions,
        TestDependencyManager    $dependencyManager,
        ProductCollectionFactory $productCollectionFactory,
        StoresUtil               $storesUtil,
        UrlPathManager           $urlPathManager
    )
    {
        parent::__construct($solutions, $dependencyManager);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storesUtil = $storesUtil;
        $this->urlPathManager = $urlPathManager;
    }

    public function configure()
    {
        $this->setName('Product with url path');
        $this->setCode('product-with-url-path');
        $this->setDescriptionTemplate("The product with SKU {sku} has a url path assigned to it for store with ID {store_id}");
    }

    public function run()
    {
        try {
            $storeIds = $this->storesUtil->getAllStoreIds();
            foreach ($storeIds as $storeId) {
                $storeId = (int)$storeId;
                $collection = $this->getProductsHavingUrlPaths($storeId);

                foreach ($collection as $product) {
                    $urlPathIsOverridenForStore = $this->urlPathManager->isOverridenInStore($product, $storeId);
                    $isDefaultStore = $storeId === Store::DEFAULT_STORE_ID;
                    if ($isDefaultStore || $urlPathIsOverridenForStore) {
                        if (is_numeric($product->getEntityId())) {
                            $this->addIssue(['product_id' => $product->getEntityId(), 'store_id' => (string) $storeId],
                                ['sku' => $product->getSku(), 'store_id' => (string) $storeId]);
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logError($exception, $this->getCode());
        }
    }

    /**
     * Gets a collection of products with url paths for the given store
     *
     * @param int $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductsHavingUrlPaths(int $storeId)
    {
        // we need a left join when using the non-default store view
        // and especially for the case where storeId 0 doesn't have a value set for this attribute
        $joinType = $storeId === Store::DEFAULT_STORE_ID ? 'inner' : 'left';

        $collection = $this->productCollectionFactory->create();
        $collection
            ->setStoreId($storeId)
            ->addAttributeToSelect(UrlPathManager::ATTRIBUTE_CODE)
            ->addAttributeToFilter(UrlPathManager::ATTRIBUTE_CODE, ['notnull' => true], $joinType);

        return $collection;
    }
}
