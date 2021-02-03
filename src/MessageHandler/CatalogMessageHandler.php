<?php
namespace App\MessageHandler;

use App\Entity\Catalog;
use App\Message\CatalogMessage;
use App\Repository\CatalogRepository;
use App\Service\CatalogManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CatalogMessageHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $catalogRepository;
    private $catalogManager;
    private $bus;
    private $workflow;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, CatalogRepository $catalogRepository, CatalogManager $catalogManager, MessageBusInterface $bus, WorkflowInterface $catalogStateMachine, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->catalogRepository = $catalogRepository;
        $this->catalogManager = $catalogManager;
        $this->bus = $bus;
        $this->workflow = $catalogStateMachine;
        $this->logger = $logger;
    }

    public function __invoke(CatalogMessage $message)
    {
        $catalog = $this->catalogRepository->find($message->getId());

        if (!$catalog instanceof Catalog) {
            return;
        }

        if ($this->workflow->can($catalog, 'handle')) {
            //handle json file and import products
            $handled = $this->catalogManager->handle($catalog);

            if ($handled) { //handling ok, change state to 'imported'
                $transition = 'handle';
                $this->workflow->apply($catalog, $transition);
                $this->entityManager->flush();
                $this->bus->dispatch($message);
            }
        } elseif ($this->logger) {
            $this->logger->debug('Dropping catalog message', ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }

        $this->entityManager->flush();
    }
}
