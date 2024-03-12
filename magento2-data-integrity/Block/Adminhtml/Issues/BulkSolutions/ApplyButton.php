<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Block\Adminhtml\Issues\BulkSolutions;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ApplyButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Save route
     *
     * @var string
     */
    protected $saveRoute = '*/*/';

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
            'label' => __('Apply'),
            'class' => 'apply primary',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'save']
                ],
                'form-role' => 'save',
            ],
            'sort_order' => 50,
        ];
    }

    /**
     * Get URL for save button
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->urlBuilder->getUrl($this->saveRoute);
    }
}
