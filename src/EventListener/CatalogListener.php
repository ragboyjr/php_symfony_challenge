<?php

namespace App\EventListener;

use App\Entity\Catalog;
use App\Message\CatalogMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class CatalogListener
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $catalogsDir;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus, string $catalogsDir)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        $this->catalogsDir = $catalogsDir;
    }

    public function onEasyAdminPrePersist(GenericEvent $event)
    {
        if (!file_exists($this->catalogsDir)) {
            mkdir($this->catalogsDir, 0777);
        }
    }

    public function onEasyAdminPostPersist(GenericEvent $event)
    {
        if (!$event->getSubject() instanceof Catalog) {
            return;
        }

        $this->catalog = $event->getSubject();

        $this->bus->dispatch(new CatalogMessage($this->catalog->getId()));
    }

    public function onEasyAdminPreRemove(GenericEvent $event)
    {
        if (!$event->getSubject() instanceof Catalog) {
            return;
        }

        $this->catalog = $event->getSubject();

        //remove json file from filesystem
        $absolutePath = $this->catalogsDir.'/'.$this->catalog->getFilePath();
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }
    }
}
