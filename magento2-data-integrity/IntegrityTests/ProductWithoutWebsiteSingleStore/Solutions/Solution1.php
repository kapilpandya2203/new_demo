<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithoutWebsiteSingleStore\Solutions;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\TestFramework\Inspection\Exception;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Model\AbstractSolution;
use Salecto\DataIntegrity\Model\SolutionDependencyManager;
use Salecto\DataIntegrity\Model\SolutionInterface;

class Solution1 extends AbstractSolution implements SolutionInterface
{
    protected string $name = 'Solution 1';
    protected string $code = 'solution1';
    protected ProductRepositoryInterface $productRepository;
    protected WebsiteFactory $websiteFactory;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        SolutionDependencyManager  $dependencyManager,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface      $storeManager,
        WebsiteFactory             $websiteFactory
    )
    {
        parent::__construct($dependencyManager);
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->websiteFactory = $websiteFactory;
    }

    public function execute(IntegrityIssuesInterface $issue): bool
    {
        $isSolved = false;

        $issueData = $issue->getIssueData();
        $issueId = (int) $issue->getId();
        $productId = $issueData['product_id'];

        if ($this->storeManager->isSingleStoreMode()) {
            $website = $this->websiteFactory->create();
            $websiteId = $website->getId();

            try {
                $product = $this->productRepository->getById($productId);
                $product->setWebsiteIds([$websiteId]);
                $this->productRepository->save($product);
                $this->dependencyManager->getIssueRepository()->delete($issue);
                $isSolved = true;
            } catch (NoSuchEntityException $exception) {
                $this->logError($exception, $issueId, __('The product with id %1 was not found in the product repository.', $productId));
            } catch (CouldNotSaveException|InputException|StateException $exception) {
                $this->logError($exception, $issueId, __('Could not save the product with ID %1.', $productId) );
            } catch (Exception $exception) {
                $this->logError($exception, $issueId);
            }
        }

        return $isSolved;
    }
}
