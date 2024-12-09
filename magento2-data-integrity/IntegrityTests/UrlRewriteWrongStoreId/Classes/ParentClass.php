<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\UrlRewriteWrongStoreId\Classes;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResource;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory as UrlRewriteModelFactory;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Model\AbstractSolution;
use Salecto\DataIntegrity\Model\SolutionDependencyManager;

abstract class ParentClass extends AbstractSolution
{
    protected UrlRewriteCollectionFactory $urlRewriteCollectionFactory;

    protected UrlRewriteResource $urlRewriteResource;

    protected UrlRewriteModelFactory $urlRewriteModelFactory;

    public function __construct(
        SolutionDependencyManager   $dependencyManager,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        UrlRewriteResource          $urlRewriteResource,
        UrlRewriteModelFactory      $urlRewriteModelFactory
    )
    {
        parent::__construct($dependencyManager);
        $this->urlRewriteResource = $urlRewriteResource;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->urlRewriteModelFactory = $urlRewriteModelFactory;
    }

    /**
     * @param IntegrityIssuesInterface $issue
     * @param bool $createOnRightLevel
     * @return bool
     */
    public function solveIssue(IntegrityIssuesInterface $issue, $createOnRightLevel = false)
    {
        $isSolved = false;
        $issueData = $issue->getIssueData();
        $urlRewriteId = $issueData['url_rewrite_id'];
        $storeId = (int) $issueData['store_id'];
        $issueId = (int)$issue->getId();
        $issueRepository = $this->dependencyManager->getIssueRepository();
        $collection = $this->urlRewriteCollectionFactory->create();
        $urlRewrite = $collection->addFieldToFilter('url_rewrite_id', $urlRewriteId)->getFirstItem();
        try {
            if ($urlRewrite instanceof UrlRewrite) {
                if ($createOnRightLevel === true) {
                    $correctUrlRewriteExists = $this->urlRewriteCollectionFactory->create()
                            ->addFieldToFilter('request_path', $urlRewrite->getRequestPath())
                            ->addFieldToFilter('target_path', $urlRewrite->getTargetPath())
                            ->addFieldToFilter('store_id', $storeId)
                            ->getSize() > 0;
                    if (!$correctUrlRewriteExists) {
                        $newUrlRewrite = $this->urlRewriteModelFactory->create();
                        $newUrlRewrite->setData($urlRewrite->getData());
                        $newUrlRewrite->setStoreId($storeId);
                        $newUrlRewrite->setId(null);
                        $this->urlRewriteResource->save($newUrlRewrite);
                    }
                }
                $this->urlRewriteResource->delete($urlRewrite);
                $issueRepository->delete($issue);
                $isSolved = true;
            } else {
                $this->dependencyManager->getLogger()
                    ->error(__('Could not find URL rewrite with ID %1 for issue with ID %2', $urlRewriteId, $issue->getId()));
            }
        } catch (AlreadyExistsException $exception) {
            $this->logError($exception, $issueId, __('The url rewrite already exists.'));
        } catch (Exception $exception) {
            $this->logError($exception, $issueId);
        }

        return $isSolved;
    }
}
