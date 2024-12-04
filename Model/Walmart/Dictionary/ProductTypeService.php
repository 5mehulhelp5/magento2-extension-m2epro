<?php

declare(strict_types=1);

namespace Ess\M2ePro\Model\Walmart\Dictionary;

class ProductTypeService
{
    private \Ess\M2ePro\Model\Walmart\Dictionary\ProductType\Repository $productTypeDictionaryRepository;
    private \Ess\M2ePro\Model\Walmart\Connector\ProductType\GetInfo\Processor $getInfoConnectProcessor;
    private \Ess\M2ePro\Model\Walmart\Dictionary\ProductTypeFactory $productTypeDictionaryFactory;

    public function __construct(
        \Ess\M2ePro\Model\Walmart\Dictionary\ProductType\Repository $productTypeDictionaryRepository,
        \Ess\M2ePro\Model\Walmart\Dictionary\ProductTypeFactory $productTypeDictionaryFactory,
        \Ess\M2ePro\Model\Walmart\Connector\ProductType\GetInfo\Processor $getInfoConnectProcessor,
        \Ess\M2ePro\Model\Walmart\Dictionary\Marketplace\Repository $marketplaceDictionaryRepository
    ) {
        $this->productTypeDictionaryRepository = $productTypeDictionaryRepository;
        $this->getInfoConnectProcessor = $getInfoConnectProcessor;
        $this->productTypeDictionaryFactory = $productTypeDictionaryFactory;
    }

    public function retrieve(
        string $productTypeNick,
        \Ess\M2ePro\Model\Marketplace $marketplace
    ): \Ess\M2ePro\Model\Walmart\Dictionary\ProductType {
        if (!$marketplace->isComponentModeWalmart()) {
            throw new \LogicException('Marketplace is not Walmart component mode.');
        }

        $productTypeDictionary = $this->productTypeDictionaryRepository->findByNick(
            $productTypeNick,
            (int)$marketplace->getId()
        );

        if ($productTypeDictionary !== null) {
            return $productTypeDictionary;
        }

        $response = $this->getInfoConnectProcessor->process(
            $productTypeNick,
            $marketplace
        );

        $productTypeDictionary = $this->productTypeDictionaryFactory->create(
            (int)$marketplace->getId(),
            $productTypeNick,
            $response->getTitle(),
            $response->getAttributes(),
            $response->getVariationAttributes()
        );
        $this->productTypeDictionaryRepository->create($productTypeDictionary);

        return $productTypeDictionary;
    }
}