<?php

namespace Ess\M2ePro\Model\ChangeTracker\Base;

/**
 * Query Factory
 */
class InventoryTrackerFactory implements TrackerFactoryInterface
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $channel
     *
     * @return \Ess\M2ePro\Model\ChangeTracker\Base\TrackerInterface
     */
    public function create(string $channel): TrackerInterface
    {
        switch ($channel) {
            case TrackerInterface::CHANNEL_EBAY:
                $class = \Ess\M2ePro\Model\ChangeTracker\Ebay\InventoryTracker::class;
                break;
            case TrackerInterface::CHANNEL_AMAZON:
                $class = \Ess\M2ePro\Model\ChangeTracker\Amazon\InventoryTracker::class;
                break;
            case TrackerInterface::CHANNEL_WALMART:
                $class = \Ess\M2ePro\Model\ChangeTracker\Walmart\InventoryTracker::class;
                break;
            default:
                throw new \RuntimeException('Unknown chanel ' . $channel);
        }

        return $this->objectManager->create($class, [
            'channel' => $channel,
        ]);
    }
}
