<?php

namespace App\Service;

use App\Entity\Catalog;
use Psr\Log\LoggerInterface;

class CatalogManager
{
    private $catalogsDir;
    private $logger;

    public function __construct(string $catalogsDir, LoggerInterface $logger = null)
    {
        $this->catalogsDir = $catalogsDir;
        $this->logger = $logger;
    }

    /**
     * @param Catalog $catalog
     * @return bool
     */
    public function handle(Catalog $catalog): bool
    {
        try {
            //process file and store each row as a new Product
            return true;
        } catch (\Exception $e) {
            $this->logger->error('An Exception has ocurred: '. $e->getMessage(), ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }

        return false;
    }
}
