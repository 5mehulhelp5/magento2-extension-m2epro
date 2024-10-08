<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Walmart\Listing\Product\Add\SourceMode;

class Product extends \Ess\M2ePro\Block\Adminhtml\Magento\Grid\AbstractContainer
{
    /** @var \Ess\M2ePro\Helper\Data */
    private $dataHelper;

    /** @var \Ess\M2ePro\Helper\Data\GlobalData */
    private $globalDataHelper;

    public function __construct(
        \Ess\M2ePro\Block\Adminhtml\Magento\Context\Widget $context,
        \Ess\M2ePro\Helper\Data $dataHelper,
        \Ess\M2ePro\Helper\Data\GlobalData $globalDataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->globalDataHelper = $globalDataHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('ListingAddFromProductList');
        $this->_controller = 'adminhtml_walmart_listing_product_add_sourceMode_product';
        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
        // ---------------------------------------

        // ---------------------------------------
        if ($this->getRequest()->getParam('back') === null) {
            $url = $this->getUrl('*/walmart_listing_product_add/index', [
                'id' => $this->getRequest()->getParam('id'),
                'wizard' => $this->getRequest()->getParam('wizard'),
                'new_listing' => $this->getRequest()->getParam('new_listing', 0),
            ]);
        } else {
            $url = $this->dataHelper->getBackUrl(
                '*/walmart_listing/index'
            );
        }
        $this->addButton('back', [
            'label' => __('Back'),
            'onclick' => 'ListingProductGridObj.backClick(\'' . $url . '\')',
            'class' => 'back',
        ]);

        // ---------------------------------------
        $this->addButton('auto_action', [
            'label' => __('Auto Add/Remove Rules'),
            'onclick' => 'ListingAutoActionObj.loadAutoActionHtml();',
            'class' => 'action-primary',
        ]);
        // ---------------------------------------

        // ---------------------------------------
        if ($this->getRequest()->getParam('new_listing')) {
            $url = $this->getUrl(
                '*/walmart_listing_product_add/exitToListing',
                ['id' => $this->getRequest()->getParam('id')]
            );
            $confirm =
                '<strong>' . __('Are you sure?') . '</strong><br><br>'
                . __('All unsaved changes will be lost and you will be returned to the Listings grid.');
            $this->addButton(
                'exit_to_listing',
                [
                    'label' => __('Cancel'),
                    'onclick' => "confirmSetLocation('$confirm', '$url');",
                    'class' => 'action-primary',
                ]
            );
        }

        $this->addButton('add_products_mode_product_continue', [
            'label' => __('Continue'),
            'onclick' => 'ListingProductGridObj.saveClick(\'view\')',
            'class' => 'action-primary forward',
        ]);
        // ---------------------------------------
    }

    protected function _prepareLayout()
    {
        $this->css->addFile('listing/autoAction.css');

        $this->jsPhp->addConstants(
            $this->dataHelper->getClassConstants(\Ess\M2ePro\Model\Listing::class)
        );

        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        $listing = $this->globalDataHelper->getValue('listing_for_products_add');

        $viewHeaderBlock = $this->getLayout()->createBlock(
            \Ess\M2ePro\Block\Adminhtml\Listing\View\Header::class,
            '',
            ['data' => ['listing' => $listing]]
        );

        $this->jsUrl->addUrls(
            $this->dataHelper->getControllerActions(
                'Walmart_Listing_AutoAction',
                ['listing_id' => $this->getRequest()->getParam('id')]
            )
        );

        $path = 'walmart_listing_autoAction/getProductTypesList';
        $this->jsUrl->add(
            $this->getUrl('*/' . $path, [
                'marketplace_id' => $listing->getMarketplaceId(),
            ]),
            $path
        );

        $this->jsTranslator->addTranslations([
            'Remove Category' => __('Remove Category'),
            'Add New Rule' => __('Add New Rule'),
            'Add/Edit Categories Rule' => __('Add/Edit Categories Rule'),
            'Auto Add/Remove Rules' => __('Auto Add/Remove Rules'),
            'Based on Magento Categories' => __('Based on Magento Categories'),
            'You must select at least 1 Category.' => __('You must select at least 1 Category.'),
            'Rule with the same Title already exists.' => __('Rule with the same Title already exists.'),
            'Rule not created' => __('Rule not created'),
            'Please select at least one action from the available options' =>
                __('Please select at least one action from the available options'),
        ]);

        $this->js->addOnReadyJs(
            <<<JS
    require([
        'M2ePro/Walmart/Listing/AutoAction'
    ], function(){

        window.ListingAutoActionObj = new WalmartListingAutoAction();

    });
JS
        );

        $hideOthersListingsProductsFilterBlock = $this->getLayout()
                                                      ->createBlock(
                                                          \Ess\M2ePro\Block\Adminhtml\Listing\Product\ShowOthersListingsProductsFilter::class
                                                      )
                                                      ->setData([
                                                          'component_mode' => \Ess\M2ePro\Helper\Component\Walmart::NICK,
                                                          'controller' => 'walmart_listing_product_add',
                                                      ]);

        return $viewHeaderBlock->toHtml()
            . '<div class="filter_block">'
            . $hideOthersListingsProductsFilterBlock->toHtml()
            . '</div>'
            . parent::getGridHtml();
    }

    protected function _toHtml()
    {
        return '<div id="add_products_progress_bar"></div>' .
            '<div id="add_products_container">' .
            parent::_toHtml() .
            '</div>';
    }
}
