<?php

declare(strict_types=1);

namespace Ess\M2ePro\Model\ResourceModel\Ebay\Listing\Wizard\Step;

use Ess\M2ePro\Model\ResourceModel\ActiveRecord\Collection\AbstractModel;

class Collection extends AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Ess\M2ePro\Model\Ebay\Listing\Wizard\Step::class,
            \Ess\M2ePro\Model\ResourceModel\Ebay\Listing\Wizard\Step::class
        );
    }
}
