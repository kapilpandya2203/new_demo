<?php

namespace Mageplaza\CustomerApproval\Plugin;

use Mageplaza\CustomerApproval\Model\Config\Source\AttributeOptions as OriginalAttributeOptions;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Data\Storage;
use Magento\Customer\Model\Session as CustomerSession;


class AttributeOptionsPlugin
{
    /**
     * Plugin method to override getAllOptions
     *
     * @param OriginalAttributeOptions $subject
     * @param array $options
     * @return array
     */

    public function __construct(
        private Config $config,
        private Storage $storage,
        private readonly CustomerSession $customerSession

    ) {
    }
    public function afterGetAllOptions(OriginalAttributeOptions $subject, $options)
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("------- afterGetAllOptions-----------------");

        $isShouldCreatedAccount = $this->storage->get(Storage::IS_SHOULD_CREATED_ACCOUNT);

        $createAccConfig = $this->config->isAllowedCreateAccountAfterCheckout();

        if ($isShouldCreatedAccount) {

            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("------- Is on-----------------");
            if ($createAccConfig) {
                $options[] = [
                    'value' =>   OriginalAttributeOptions::NEW_STATUS,
                    'label' => __('New')
                ];
            }
        } else {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("------- Is off-----------------");
        }

        // Return the modified options array
        return $options;
    }
}
