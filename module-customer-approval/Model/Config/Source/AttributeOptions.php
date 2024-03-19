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
 * @package   Mageplaza_CustomerApproval
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerApproval\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\RequestInterface;

/**
 * Class AttributeOptions
 * @package Mageplaza\CustomerApproval\Model\Config\Source
 */
class AttributeOptions extends AbstractSource
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const NOTAPPROVE = 'notapproved';
    const NEW_STATUS = 'new';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];

        foreach ($this->toArray() as $key => $label) {
            $options[] = [
                'value' => $key,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $formKey = $this->request->getPostValue('form_key');
        $data = [
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::NOTAPPROVE => __('Not Approved')
        ];
        if ($formKey) {
            $data = [
                self::NEW_STATUS => __('New'),
                self::PENDING => __('Pending'),
                self::APPROVED => __('Approved'),
                self::NOTAPPROVE => __('Not Approved')
            ];
        }
        return $data;
    }
}
