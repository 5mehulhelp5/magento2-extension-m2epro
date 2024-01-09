<?php

/**
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Order;

class SaveNote extends \Ess\M2ePro\Controller\Adminhtml\Order
{
    /** @var \Ess\M2ePro\Model\Order\Note\Repository */
    private $noteRepository;

    public function __construct(
        \Ess\M2ePro\Model\Order\Note\Repository $noteRepository,
        \Ess\M2ePro\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->noteRepository = $noteRepository;
    }

    public function execute()
    {
        $noteText = $this->getRequest()->getParam('note');
        if ($noteText === null) {
            $this->setJsonContent(['result' => false]);

            return $this->getResult();
        }

        if ($noteId = $this->getRequest()->getParam('note_id')) {
            $noteModel = $this->noteRepository->get($noteId);
            $noteModel->setNote($noteText);
            $this->noteRepository->save($noteModel);
        } else {
            $this->noteRepository->create($this->getRequest()->getParam('order_id'), $noteText);
        }

        $this->setJsonContent(['result' => true]);

        return $this->getResult();
    }
}
