<?php

declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\AttributeValueDuplicacy\Solutions;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Model\AbstractSolution;
use Salecto\DataIntegrity\Model\SolutionDependencyManager;
use Salecto\DataIntegrity\Model\SolutionInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

class Solution1 extends AbstractSolution implements SolutionInterface
{
    protected string $name = 'Solution 1';
    protected string $code = 'solution 1';
    protected ProductRepositoryInterface $productRepository;
    protected ProductAttributeRepositoryInterface $attributeRepository;
    protected AttributeOptionManagementInterface $attributeOptionManagement;

    public function __construct(
        SolutionDependencyManager $dependencyManager,
        ProductRepositoryInterface $productRepository,
        ProductAttributeRepositoryInterface $attributeRepository,
        AttributeOptionManagementInterface $attributeOptionManagement
    ) {
        parent::__construct($dependencyManager);
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
    }

    public function execute(IntegrityIssuesInterface $issue): bool
    {
        $issueData = $issue->getIssueData();
        $maxProductCount = null;
        $optionIdWithMaxProductCount = null;

        foreach ($issueData as $attributesData) {
            foreach ($attributesData['option_id_with_product_ids'] as $optionId => $productIds) {
                $productCount = count($productIds['product_ids']);
                if ($productCount > $maxProductCount) {
                    $maxProductCount = $productCount;
                    $optionIdWithMaxProductCount = $optionId;
                }
            }
        }

        try {
            // Update product attributes based on $optionIdWithMaxProductCount
            if ($optionIdWithMaxProductCount !== null) {
                $this->updateProductAttributeCodesValues($issueData, $optionIdWithMaxProductCount);
                $this->deleteAttributeOptions($issueData, $optionIdWithMaxProductCount);
            }

            // Delete other option IDs from the attribute
            // if ($optionIdWithMaxProductCount !== null) {
            //     $this->deleteAttributeOptions($issueData, $optionIdWithMaxProductCount);
            // }
        } catch (\Exception $exception) {
            $this->logError($exception, $issueData, __('Could not update options.'));
            return false;
        }
        return true;
    }

    protected function updateProductAttributeCodesValues(array $issueData, int $optionIdWithMaxProductCount): void
    {
        foreach ($issueData as $attributesData) {
            $attributeCode = $attributesData['attribute_code'];
            foreach ($attributesData['option_id_with_product_ids'] as $optionId => $productIds) {
                foreach ($productIds['product_ids'] as $storeId => $ids) {
                    foreach ($ids as $productId) {
                        $product = $this->productRepository->getById($productId, false, $storeId);
                        $product->setData($attributeCode, $optionIdWithMaxProductCount)->save();
                    }
                }
            }
        }
    }

    protected function deleteAttributeOptions(array $issueData, int $optionIdWithMaxProductCount): void
    {
        foreach ($issueData as $attributesData) {
            foreach ($attributesData['option_id_with_product_ids'] as $optionId => $productIds) {
                if ($optionId != $optionIdWithMaxProductCount) {
                    $this->attributeOptionManagement->delete(
                        'catalog_product',
                        $attributesData['attribute_code'],
                        $optionId
                    );
                }
            }
        }
    }
}
