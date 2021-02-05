<?php
namespace App\Command;

use App\Entity\Catalog;
use App\Repository\CatalogRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $catalogsDir;

    protected static $defaultName = 'app:catalog:sync';

    public function __construct(string $catalogsDir, CatalogRepository $catalogRepository, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->catalogRepository = $catalogRepository;
        $this->catalogsDir = $catalogsDir;
        $this->bus = $bus;
        $this->entityManager = $entityManager;

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
        }

        $io->success(sprintf('Synced "%d" catalogs.', $count));

        return 0;
    }
}
