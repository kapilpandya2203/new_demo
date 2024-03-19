<?php

namespace Mageplaza\CustomerApproval\Plugin;

use Mageplaza\CustomerApproval\Model\Config\Source\AttributeOptions as OriginalAttributeOptions;
use Aheadworks\OneStepCheckout\Model\Config;
use Aheadworks\OneStepCheckout\Model\Data\Storage;

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

    ) {
    }
    public function afterGetAllOptions(OriginalAttributeOptions $subject, $options)
    {
        $isShouldCreatedAccount = $this->storage->get(Storage::IS_SHOULD_CREATED_ACCOUNT);
        $createAccConfig = $this->config->isAllowedCreateAccountAfterCheckout();

        if ($isShouldCreatedAccount) {
            if ($createAccConfig) {
                $options[] = [
                    'value' =>   OriginalAttributeOptions::NEW_STATUS,
                    'label' => __('New')
                ];
            }
        }
        return $options;
    }
}
