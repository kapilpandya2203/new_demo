<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Solutions;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Classes\UrlPathManager;
use Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Classes\UrlRewriteManager;
use Salecto\DataIntegrity\Model\AbstractSolution;
use Salecto\DataIntegrity\Model\SolutionDependencyManager;
use Salecto\DataIntegrity\Model\SolutionInterface;

class Solution1 extends AbstractSolution implements SolutionInterface
{
    protected string $name = 'Solution 1';
    protected string $code = 'solution 1';
    protected ProductRepositoryInterface $productRepository;
    protected UrlRewriteManager $urlRewriteManager;
    protected UrlPathManager $urlPathManager;

    public function __construct(
        SolutionDependencyManager  $dependencyManager,
        UrlRewriteManager          $urlRewriteManager,
        ProductRepositoryInterface $productRepository,
        UrlPathManager             $urlPathManager
    )
    {
        parent::__construct($dependencyManager);
        $this->urlRewriteManager = $urlRewriteManager;
        $this->productRepository = $productRepository;
        $this->urlPathManager = $urlPathManager;
    }

    public function execute(IntegrityIssuesInterface $issue): bool
    {
        $issueData = $issue->getIssueData();
        $issueId = (int)$issue->getId();
        $productId = (int)$issueData['product_id'];
        $storeId = (int)$issueData['store_id'];
        $urlPathAttributeValue = $this->urlPathManager->getAttributeValue($productId, $storeId);

        try {
            if (!empty($urlPathAttributeValue)) {
                $urlPathValue = $urlPathAttributeValue['url_path_value'];
                $urlPathValueId = $urlPathAttributeValue['url_path_value_id'];
                $this->urlPathManager->deleteById($urlPathValueId);
                $product = $this->productRepository->getById($productId);

                if ($storeId === Store::DEFAULT_STORE_ID) {
                    foreach ($product->getStoreIds() as $id) {
                        $id = (int) $id;
                        $urlPathIsOverridenForStore = $this->urlPathManager->isOverridenInStore($product, $id);
                        $isDefaultStore = $id == Store::DEFAULT_STORE_ID;
                        if ($id !== null && !$isDefaultStore && !$urlPathIsOverridenForStore) {
                            $this->fixForStore($product, $id, $urlPathValue);
                        }
                    }
                } else {
                    $this->fixForStore($product, $storeId, $urlPathValue);
                }

                $this->dependencyManager->getIssueRepository()->delete($issue);
            }
        } catch (NoSuchEntityException $exception) {
            $this->logError($exception, $issueId, __('The product with id %1 was not found in the product repository.', $productId));
        } catch (AlreadyExistsException $exception) {
            $this->logError($exception, $issueId, __('The url rewrite already exists.'));
        } catch (Exception $exception) {
            $this->logError($exception, $issueId, __('Could not delete the url rewrite.'));
        }

        return true;
    }

    /**
     * @param ProductInterface $product
     * @param int $storeId
     * @param string $urlPathValue
     * @return void
     * @throws AlreadyExistsException
     * @throws UrlAlreadyExistsException
     * @throws LocalizedException
     */
    protected function fixForStore(ProductInterface $product, int $storeId, string $urlPathValue)
    {
        $urlRewriteManager = $this->urlRewriteManager;
        $urlPathRewriteExists = $urlRewriteManager->urlPathRewriteExists($urlPathValue, $storeId);

        $urlRewriteManager->deleteProductUrlRewrites((int)$product->getId(), $storeId);

        $newUrls = $urlRewriteManager->generateAndGetUrlRewrites($product, $storeId);

        $urlRewriteManager->replaceUrlRewrites($newUrls);

        if ($urlPathRewriteExists) {
            $urlKey = $product->getUrlKey();
            if ($urlRewriteManager->urlKeyRewriteExist($urlKey, $storeId)) {
                $urlRewriteManager->redirectUrlPathToUrlKey($product->getId(), $urlPathValue, $urlKey, $storeId);
            }
        }
    }
}
