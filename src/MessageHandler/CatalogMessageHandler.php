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
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    /**
     * @var CatalogManager
     */
    private $catalogManager;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * @var WorkflowInterface
     */
    private $workflow;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CatalogMessageHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param CatalogRepository $catalogRepository
     * @param CatalogManager $catalogManager
     * @param MessageBusInterface $bus
     * @param WorkflowInterface $catalogStateMachine
     * @param LoggerInterface|null $logger
     */
    public function __construct(EntityManagerInterface $entityManager, CatalogRepository $catalogRepository, CatalogManager $catalogManager, MessageBusInterface $bus, WorkflowInterface $catalogStateMachine, LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->catalogRepository = $catalogRepository;
        $this->catalogManager = $catalogManager;
        $this->bus = $bus;
        $this->workflow = $catalogStateMachine;
        $this->logger = $logger;
    }

    /**
     * @param CatalogMessage $message
     */
    public function __invoke(CatalogMessage $message)
    {
        $catalog = $this->catalogRepository->find($message->getId());

        if (!$catalog instanceof Catalog) {
            return;
        }
        $this->logger->debug($this->workflow->can($catalog, 'handle'));
        $this->logger->debug($this->workflow->can($catalog, 'sync'));

        if ($this->workflow->can($catalog, 'handle')) {
            //handle json file and import products
            $handled = $this->catalogManager->handle($catalog);
            if ($handled) { //handling ok, change state to 'imported'
                $this->workflow->apply($catalog, 'handle');
                $this->entityManager->flush();
                $this->bus->dispatch($message);
            }
        } elseif ($this->workflow->can($catalog, 'sync')) {
            //export products to csv in order to be synced by sftp
            $synced = $this->catalogManager->export($catalog);

            if ($synced) { //sync ok, change state to 'synced'
                $this->workflow->apply($catalog, 'sync');
                $this->entityManager->flush();
                $this->bus->dispatch($message);
            }
        } elseif ($this->logger) {
            $this->logger->debug('Dropping catalog message', ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }
    }
}
