<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithoutWebsiteSingleStore;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Salecto\DataIntegrity\IntegrityTests\ProductWithoutWebsiteSingleStore\Classes\ProductWithoutWebsiteCollection;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\TestDependencyManager;
use Salecto\DataIntegrity\Model\TestInterface;

class Test extends AbstractTest implements TestInterface
{
    protected StoreManagerInterface $storeManager;
    protected ProductWithoutWebsiteCollection $productWithoutWebsiteCollection;

    public function __construct(
        array                           $solutions,
        TestDependencyManager           $dependencyManager,
        StoreManagerInterface           $storeManager,
        ProductWithoutWebsiteCollection $productWithoutWebsiteCollection
    )
    {
        parent::__construct($solutions, $dependencyManager);
        $this->storeManager = $storeManager;
        $this->productWithoutWebsiteCollection = $productWithoutWebsiteCollection;
    }

    public function configure()
    {
        $this->setName('Product without website - single store');
        $this->setCode('product-without-website-single-store');
        $this->setDescriptionTemplate("The product with ID {product_id} does not have a website assigned.");
    }

    public function run()
    {
        try {
            if ($this->storeManager->isSingleStoreMode()) {
                $productIds = $this->productWithoutWebsiteCollection->getProductIds();
                if (count($productIds) > 0) {
                    foreach ($productIds as $productId) {
                        $this->addIssue(['product_id' => $productId], ['product_id' => $productId]);
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logError($exception, $this->getCode());
        }
    }
}
