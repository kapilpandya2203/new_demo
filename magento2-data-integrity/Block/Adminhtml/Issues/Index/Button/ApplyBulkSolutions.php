<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Block\Adminhtml\Issues\Index\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ApplyBulkSolutions implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Apply bulk solutions route
     *
     * @var string
     */
    protected $bulkSolutionsRoute = 'data_integrity/issues/bulksolutions';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Apply Bulk Solutions'),
            'class' => 'bulk-solutions primary',
            'on_click' => sprintf("location.href = '%s';", $this->getApplyBulkSolutionsUrl()),
            'sort_order' => 30,
        ];
    }

    /**
     * Retrieve the Url for apply bulk solutions.
     *
     * @return string
     */
    public function getApplyBulkSolutionsUrl()
    {
        return $this->urlBuilder->getUrl($this->bulkSolutionsRoute);
    }
}
