<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_BetterMaintenance
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerApproval\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class RedirectTo
 *
 * @package Mageplaza\BetterMaintenance\Model\Config\Source\System
 */
class RedirectTo implements ArrayInterface
{

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * RedirectTo constructor.
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_pageFactory->create()->addFieldToFilter('is_active', 1)->toOptionIdArray();
    }
}
