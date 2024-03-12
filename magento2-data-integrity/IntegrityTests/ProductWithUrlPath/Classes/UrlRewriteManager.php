<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Classes;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogUrlRewrite\Model\ProductScopeRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResource;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory as UrlRewriteModelFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class UrlRewriteManager
{
    protected UrlRewriteCollectionFactory $urlRewriteCollectionFactory;
    protected UrlRewriteResource $urlRewriteResource;
    protected UrlPersistInterface $urlPersist;
    protected UrlRewriteModelFactory $urlRewriteFactory;
    protected ProductScopeRewriteGenerator $productScopeRewriteGenerator;

    public function __construct(
        UrlRewriteCollectionFactory  $urlRewriteCollectionFactory,
        UrlRewriteResource           $urlRewriteResource,
        UrlRewriteModelFactory       $urlRewriteFactory,
        UrlPersistInterface          $urlPersist,
        ProductScopeRewriteGenerator $productScopeRewriteGenerator
    )
    {
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->urlRewriteResource = $urlRewriteResource;
        $this->urlPersist = $urlPersist;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->productScopeRewriteGenerator = $productScopeRewriteGenerator;
    }

    /**
     * Checks to see if a url rewrite exists for the url key
     *
     * @param string $urlKey
     * @param int $storeId
     * @return bool
     */
    public function urlKeyRewriteExist(string $urlKey, int $storeId)
    {
        return $this->rewriteExistsForRequestPath($urlKey, $storeId);
    }

    /**
     * returns whether a url rewrite exists for the url path
     *
     * @param string $urlPathValue
     * @param int $storeId
     * @return bool
     */
    public function urlPathRewriteExists(string $urlPathValue, int $storeId)
    {
        return $this->rewriteExistsForRequestPath($urlPathValue, $storeId);
    }

    /**
     * returns whether a url rewrite exists for the request path
     * @param string $requestPath
     * @param int $storeId
     * @return bool
     */
    protected function rewriteExistsForRequestPath(string $requestPath, int $storeId)
    {
        return $this->urlRewriteCollectionFactory->create()
                ->addFieldToFilter(UrlRewrite::REQUEST_PATH, $requestPath)
                ->addFieldToFilter(UrlRewrite::STORE_ID, $storeId)
                ->addFieldToFilter(UrlRewrite::REDIRECT_TYPE, 0)
                ->getSize() > 0;
    }

    /**
     * Remove rewrites which are not redirects for a product and store
     *
     * @param int $productId
     * @param int $storeId
     * @return void
     */
    public function deleteProductUrlRewrites(int $productId, int $storeId)
    {
        $this->urlPersist->deleteByData([
            UrlRewrite::ENTITY_ID => $productId,
            UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::REDIRECT_TYPE => 0,
            UrlRewrite::STORE_ID => $storeId
        ]);
    }

    /**
     * Deletes a url rewrite
     *
     * @param $urlRewrite
     * @return void
     * @throws Exception
     */
    public function deleteUrlRewrite($urlRewrite)
    {
        $this->urlRewriteResource->delete($urlRewrite);
    }

    /**
     * Generates and returns product url rewrites for a store
     *
     * @param Product $product
     * @param int $storeId
     * @return array|UrlRewrite[]
     * @throws LocalizedException
     */
    public function generateAndGetUrlRewrites(Product $product, int $storeId)
    {
        if ($product->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE) {
            return [];
        }

        $productCategories = $product->getCategoryCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');

        return $this->productScopeRewriteGenerator->generateForSpecificStoreView($storeId, $productCategories, $product);
    }

    /**
     * Save new url rewrites and remove old if exist
     *
     * @param array $urls
     * @return void
     * @throws UrlAlreadyExistsException
     */
    public function replaceUrlRewrites(array $urls)
    {
        $this->urlPersist->replace($urls);
    }

    /**
     * Creates a permanent redirect from urlPath to urlKey for a product on the given store
     *
     * @param $productId
     * @param $urlPath
     * @param $urlKey
     * @param $storeId
     * @return void
     * @throws AlreadyExistsException
     */
    public function redirectUrlPathToUrlKey($productId, $urlPath, $urlKey, $storeId)
    {
        $urlPathRedirect = $this->urlRewriteFactory->create()
            ->setStoreId($storeId)
            ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
            ->setEntityId($productId)
            ->setRequestPath($urlPath)
            ->setTargetPath($urlKey)
            ->setIsAutogenerated(1)
            ->setRedirectType(OptionProvider::PERMANENT);
        $this->urlRewriteResource->save($urlPathRedirect);
    }
}
