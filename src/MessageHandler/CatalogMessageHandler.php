<?php
namespace App\MessageHandler;

use App\Message\CatalogMessage;
use App\Repository\CatalogRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CatalogMessageHandler implements MessageHandlerInterface
{
   /* private $spamChecker;
    private $entityManager;
    private $catalogRepository;

    public function __construct(EntityManagerInterface $entityManager, SpamChecker $spamChecker, CatalogRepository $catalogRepository)
    {
        $this->entityManager = $entityManager;
        $this->spamChecker = $spamChecker;
        $this->catalogRepository = $catalogRepository;
    }
*/
    public function __invoke(CatalogMessage $message)
    {
      /*  $catalog = $this->catalogRepository->find($message->getId());
        if (!$catalog) {
            return;
        }

        if (2 === $this->spamChecker->getSpamScore($catalog, $message->getContext())) {
            $catalog->setState('spam');
        } else {
            $catalog->setState('published');
        }

        $this->entityManager->flush();*/
    }
}