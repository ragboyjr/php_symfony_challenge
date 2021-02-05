<?php

namespace App\Service;

use App\Entity\Catalog;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Service\ConversorManager;

class CatalogManager
{
    private $catalogsDir;
    private $logger;
    private $entityManager;
    private $conversorManager;

    public function __construct(string $catalogsDir, LoggerInterface $logger = null, EntityManagerInterface $entityManager, ConversorManager $conversorManager)
    {
        $this->catalogsDir = $catalogsDir;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->conversorManager = $conversorManager;
    }

    /**
     * @param Catalog $catalog
     * @return bool
     *
     * Process file and store each row as a new Product
     */
    public function handle(Catalog $catalog): bool
    {
        try {
            $path = $this->catalogsDir.'/'.$catalog->getFilePath();
            //get file contents
            if (file_exists($path)) {
                $jsonData = json_decode(file_get_contents($path), true);
                //iterate array elements an add new product each
                foreach ($jsonData as $x => $row) {
                    $product = new Product();
                    $product->setStyleNumber($row['styleNumber']);
                    $product->setName($row['name']);
                    $price = new Price();
                    $price->setCurrency($row['price']['currency']);
                    $price->setAmount($row['price']['amount']);

                    //If product have a diferent currency than USD convert do proccess convertion
                    if ($price->getCurrency() != 'USD') {
                        $price->setAmount($this->conversorManager->convertPrice($price, 'USD'));
                        $price->setCurrency('USD');
                    }

                    $this->entityManager->persist($price);

                    $product->setPrice($price);
                    $product->setImages($row['images']);
                    $catalog->addProduct($product);

                    $this->entityManager->persist($product);

                    if ($x % 1000 == 0) { //in order not to flush all the products at once
                        $this->entityManager->flush();
                    }
                }
            }

            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            $this->logger->error('An Exception has ocurred: '. $e->getMessage(), ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }

        return false;
    }

    /**
     * @param Catalog $catalog
     * @return bool
     */
    public function export(Catalog $catalog): bool
    {
        try {
            return true;
        } catch (\Exception $e) {
            $this->logger->error('An Exception has ocurred: '. $e->getMessage(), ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }

        return false;
    }
}
