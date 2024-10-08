<?php

namespace Ess\M2ePro\Block\Adminhtml\Walmart\Listing\AutoAction\Mode;

class GlobalMode extends \Ess\M2ePro\Block\Adminhtml\Listing\AutoAction\Mode\AbstractGlobalMode
{
    private \Ess\M2ePro\Helper\Module\Support $supportHelper;
    private \Ess\M2ePro\Model\Walmart\ProductType\Repository $productTypeRepository;

    public function __construct(
        \Ess\M2ePro\Model\Walmart\ProductType\Repository $productTypeRepository,
        \Ess\M2ePro\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Ess\M2ePro\Helper\Module\Support $supportHelper,
        \Ess\M2ePro\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->supportHelper = $supportHelper;
        $this->productTypeRepository = $productTypeRepository;

        parent::__construct($context, $registry, $formFactory, $dataHelper, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $form->addField(
            'global_mode_help_block',
            self::HELP_BLOCK,
            [
                'content' => $this->__(
                    '<p>These Rules of the automatic product adding and removal act globally for all Magento Catalog.
                    When a new Magento Product is added to Magento Catalog, it will be automatically added to the
                    current M2E Pro Listing if the settings are enabled.</p><br>
                    <p>Please note if a product is already presented in another M2E Pro Listing with the related
                    Channel account and marketplace, the Item won’t be added to the Listing to prevent listing
                    duplicates on the Channel.</p><br>
                    <p>Accordingly, if a Magento Product presented in the M2E Pro Listing is removed from Magento
                    Catalog, the Item will be removed from the Listing and its sale will be stopped on Channel.</p><br>
                    <p>More detailed information you can find
                    <a href="%url%" target="_blank" class="external-link">here</a>.</p>',
                    $this->supportHelper->getDocumentationArticleUrl(
                        'adding-products-automatically-auto-addremove-rules'
                    )
                ),
            ]
        );

        $form->addField(
            'auto_mode',
            'hidden',
            [
                'name' => 'auto_mode',
                'value' => \Ess\M2ePro\Model\Listing::AUTO_MODE_GLOBAL,
            ]
        );

        $fieldSet = $form->addFieldset('auto_global_fieldset_container', []);

        if ($this->formData['auto_global_adding_mode'] == \Ess\M2ePro\Model\Listing::ADDING_MODE_NONE) {
            $fieldSet->addField(
                'auto_global_adding_mode',
                self::SELECT,
                [
                    'name' => 'auto_global_adding_mode',
                    'label' => __('New Product Added to Magento'),
                    'title' => __('New Product Added to Magento'),
                    'values' => [
                        ['value' => \Ess\M2ePro\Model\Listing::ADDING_MODE_NONE, 'label' => __('No Action')],
                        ['value' => \Ess\M2ePro\Model\Listing::ADDING_MODE_ADD, 'label' => __('Add to the Listing')],
                    ],
                    'value' => \Ess\M2ePro\Model\Listing::ADDING_MODE_NONE,
                    'tooltip' => __('Action which will be applied automatically.'),
                    'style' => 'width: 350px',
                ]
            );
        } else {
            $fieldSet->addField(
                'auto_global_adding_mode',
                self::SELECT,
                [
                    'name' => 'auto_global_adding_mode',
                    'label' => __('New Product Added to Magento'),
                    'title' => __('New Product Added to Magento'),
                    'disabled' => true,
                    'values' => [
                        ['value' => \Ess\M2ePro\Model\Listing::ADDING_MODE_ADD, 'label' => __('Add to the Listing')],
                    ],
                    'value' => \Ess\M2ePro\Model\Listing::ADDING_MODE_ADD,
                    'tooltip' => __('Action which will be applied automatically.'),
                    'style' => 'width: 350px',
                ]
            );
        }

        $fieldSet->addField(
            'auto_global_adding_add_not_visible',
            self::SELECT,
            [
                'name' => 'auto_global_adding_add_not_visible',
                'label' => __('Add not Visible Individually Products'),
                'title' => __('Add not Visible Individually Products'),
                'values' => [
                    ['value' => \Ess\M2ePro\Model\Listing::AUTO_ADDING_ADD_NOT_VISIBLE_NO, 'label' => __('No')],
                    [
                        'value' => \Ess\M2ePro\Model\Listing::AUTO_ADDING_ADD_NOT_VISIBLE_YES,
                        'label' => __('Yes'),
                    ],
                ],
                'value' => $this->formData['auto_global_adding_add_not_visible'],
                'field_extra_attributes' => 'id="auto_global_adding_add_not_visible_field"',
                'tooltip' => __(
                    'Set to <strong>Yes</strong> if you want the Magento Products with
                    Visibility \'Not visible Individually\' to be added to the Listing
                    Automatically.<br/>
                    If set to <strong>No</strong>, only Variation (i.e.
                    Parent) Magento Products will be added to the Listing Automatically,
                    excluding Child Products.'
                ),
            ]
        );

        $productTypes = $this->productTypeRepository->retrieveByMarketplaceId(
            $this->getListing()->getMarketplaceId()
        );

        $options = [['label' => '', 'value' => '', 'attrs' => ['class' => 'empty']]];
        foreach ($productTypes as $productType) {
            $tmp = [
                'label' => $productType->getTitle(),
                'value' => $productType->getId(),
            ];

            $options[] = $tmp;
        }

        $url = $this->getUrl('*/walmart_productType/edit', [
            'marketplace_id' => $this->getListing()->getMarketplaceId(),
            'close_on_save' => true,
        ]);

        $fieldSet->addField(
            'adding_product_type_id',
            self::SELECT,
            [
                'name' => 'adding_product_type_id',
                'label' => __('Product Type'),
                'title' => __('Product Type'),
                'values' => $options,
                'value' => $this->formData['auto_global_adding_product_type_id'],
                'field_extra_attributes' => 'id="auto_action_walmart_add_and_assign_product_type"',
                'required' => true,
                'after_element_html' => $this->getTooltipHtml(
                    __(
                        'Select Product Type you want to assign to Product(s).<br><br>
                    <strong>Note:</strong> Submitting of Category data is required when you create a new offer on
                    Walmart. Product Type must be assigned to Products before they are added to M2E Pro Listing.'
                    )
                ) . '<a href="javascript: void(0);"
                        style="vertical-align: inherit; margin-left: 65px;"
                        onclick="ListingAutoActionObj.addNewTemplate(\'' . $url . '\',
                        ListingAutoActionObj.reloadProductTypes);">' . __('Add New') . '
                     </a>',
            ]
        );

        $fieldSet->addField(
            'auto_global_deleting_mode',
            self::SELECT,
            [
                'name' => 'auto_global_deleting_mode',
                'disabled' => 'disabled',
                'label' => __('Product Deleted from Magento'),
                'title' => __('Product Deleted from Magento'),
                'values' => [
                    [
                        'value' => \Ess\M2ePro\Model\Listing::DELETING_MODE_STOP_REMOVE,
                        'label' => __('Stop on Channel and Delete from Listing'),
                    ],
                ],
                'style' => 'width: 350px;',
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

    protected function _afterToHtml($html)
    {
        $this->jsPhp->addConstants(
            $this->dataHelper->getClassConstants(\Ess\M2ePro\Model\Walmart\Listing::class)
        );

        $this->js->add(
            <<<JS

        $('adding_product_type_id').observe('change', function(el) {
            var options = $(el.target).select('.empty');
            options.length > 0 && options[0].hide();
        });
JS
        );

        return parent::_afterToHtml($html);
    }
}
