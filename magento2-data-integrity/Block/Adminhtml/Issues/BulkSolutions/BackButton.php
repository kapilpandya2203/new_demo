<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Block\Adminhtml\Issues\BulkSolutions;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $backRoute = '*/*/';

    /**
     * Constructor
     *
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
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->urlBuilder->getUrl($this->backRoute);
    }
}
