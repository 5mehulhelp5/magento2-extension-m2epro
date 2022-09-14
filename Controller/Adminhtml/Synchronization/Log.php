<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Synchronization;

abstract class Log extends \Ess\M2ePro\Controller\Adminhtml\Base
{
    protected function initResultPage()
    {
        if ($this->resultPage !== null) {
            return;
        }

        parent::initResultPage();

        if ($this->getMenuRootNodeNick() !== null) {
            $this->getResultPage()->setActiveMenu($this->getMenuRootNodeNick());
        }
    }

    protected function getMenuRootNodeNick()
    {
        $referrer = $this->getRequest()->getParam('referrer');

        if ($referrer == \Ess\M2ePro\Helper\View\Ebay::NICK) {
            return \Ess\M2ePro\Helper\View\Ebay::MENU_ROOT_NODE_NICK;
        }
        if ($referrer == \Ess\M2ePro\Helper\View\Amazon::NICK) {
            return \Ess\M2ePro\Helper\View\Amazon::MENU_ROOT_NODE_NICK;
        }
        if ($referrer == \Ess\M2ePro\Helper\View\Walmart::NICK) {
            return \Ess\M2ePro\Helper\View\Walmart::MENU_ROOT_NODE_NICK;
        }

        return null;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ess_M2ePro::ebay_help_center_synchronization_log')
            || $this->_authorization->isAllowed('Ess_M2ePro::amazon_help_center_synchronization_log')
            || $this->_authorization->isAllowed('Ess_M2ePro::walmart_help_center_synchronization_log');
    }
}