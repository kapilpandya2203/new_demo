<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\TestDependencyManager;
use Salecto\DataIntegrity\Model\TestInterface;

class Test extends AbstractTest implements TestInterface
{
    protected UrlRewriteCollectionFactory $urlRewriteCollectionFactory;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        array                       $solutions,
        TestDependencyManager       $dependencyManager,
        StoreManagerInterface       $storeManager,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    )
    {
        parent::__construct($solutions, $dependencyManager);
        $this->storeManager = $storeManager;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    public function configure()
    {
        $this->setName('Url Rewrite Wrong Store Id');
        $this->setCode('url-rewrite-wrong-store-id');
        $this->setDescriptionTemplate("This shop is currently in single store mode, however, it has been detected that the URL rewrite entry with ID {id} has a store ID that does not match the correct store ID {storeId}");
    }

    public function run()
    {
        if ($this->storeManager->isSingleStoreMode()) {
            try {
                $storeId = $this->storeManager->getDefaultStoreView()->getId();

                $collection = $this->urlRewriteCollectionFactory->create();
                $collection->addFieldToFilter('store_id', ['neq' => $storeId]);

                foreach ($collection as $urlRewrite) {
                    $this->addIssue(['url_rewrite_id' => $urlRewrite->getId(), 'store_id' => $storeId],
                        ['id' => $urlRewrite->getId(), 'storeId' => $storeId]);
                }
            } catch (Exception $exception) {
                $this->logError($exception, $this->getCode());
            }
        }
    }
}
