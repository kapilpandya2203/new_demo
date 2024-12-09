<?php

declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\AttributeValueDuplicacy;

use Exception;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Salecto\DataIntegrity\IntegrityTests\AttributeValueDuplicacy\Classes\DuplicateProductAttributeCode;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\TestDependencyManager;
use Salecto\DataIntegrity\Model\TestInterface;

class Test extends AbstractTest implements TestInterface
{
    protected ProductAttributeRepositoryInterface $productAttributeRepository;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected $duplicateAttributes;
    public function __construct(
        array $solutions,
        TestDependencyManager $dependencyManager,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DuplicateProductAttributeCode $duplicateAttributes
    ) {
        parent::__construct($solutions, $dependencyManager);
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->duplicateAttributes = $duplicateAttributes;
    }

    public function configure()
    {
        $this->setName('Duplicate Product Attribute Codes');
        $this->setCode('duplicate-product-attribute-codes');
        $this->setDescriptionTemplate('Attribute code {attribute_code} contains duplicate option {attribute_option_label} and is assigned to {product_count} products.');
    }

    public function run()
    {
        try {
            
            $issueData = $this->duplicateAttributes->getDuplicateAttributesWithOptionsAndProductIds();
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("--------Test file----------------");
            // \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(print_r($issueData, true));

            foreach ($issueData as $issue) {
                $attributeCode = $issue['attribute_code'];
                $optionLabel = $issue['option_label'];
                $productCount = 0;
                $uniqueProductIds = [];

        
                foreach ($issue['option_id_with_product_ids'] as $productData) {
                    foreach ($productData['product_ids'] as $storeId => $storeProductIds) {
                        // Increment the product count by the number of product IDs for this store
                        $uniqueProductIds = array_merge($uniqueProductIds, $storeProductIds);
                    }
                }
                $productCount = count(array_unique($uniqueProductIds));

        
                $this->addIssue([
                    'data' => $issue
                ], [
                    'attribute_code' => $attributeCode,
                    'attribute_option_label' => $optionLabel,
                    'product_count' => (string)$productCount
                ]);
            }
        } catch (\Exception $e) {
            // Handle exception
        } catch (Exception $exception) {
            $this->logError($exception, $this->getCode());
        }
    }
}
