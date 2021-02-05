<?php
namespace App\Command;

use App\Entity\Catalog;
use App\Message\CatalogMessage;
use App\Repository\CatalogRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CatalogSyncCommand extends Command
{
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    protected static $defaultName = 'app:catalog:sync';

    public function __construct(CatalogRepository $catalogRepository, MessageBusInterface $bus)
    {
        $this->catalogRepository = $catalogRepository;
        $this->bus = $bus;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Sync imported products to sftp')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $catalogs = $this->catalogRepository->findBy(array('state' => Catalog::IMPORTED_STATE));

        $count = count($catalogs);
        foreach ($catalogs as $catalog) {
            //export csv and change state to synced
            try {
                $this->bus->dispatch(new CatalogMessage($catalog->getId()));
            } catch (\Exception $e) {
                $io->error('An Exception has ocurred: '. $e->getMessage(), ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
            }
        }

        $io->success(sprintf('Synced "%d" catalogs.', $count));

        return 0;
    }
}
