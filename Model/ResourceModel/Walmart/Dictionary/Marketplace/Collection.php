<?php

declare(strict_types=1);

namespace Ess\M2ePro\Model\ResourceModel\Walmart\Dictionary\Marketplace;

class Collection extends \Ess\M2ePro\Model\ResourceModel\ActiveRecord\Collection\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Ess\M2ePro\Model\Walmart\Dictionary\Marketplace::class,
            \Ess\M2ePro\Model\ResourceModel\Walmart\Dictionary\Marketplace::class
        );
    }
}
