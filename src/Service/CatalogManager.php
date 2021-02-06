<?php

namespace App\Service;

use App\Entity\Catalog;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CatalogManager
 * @package App\Service
 *
 * Service used to import/export catalog files
 */
class CatalogManager
{
    /**
     * @var string
     */
    private $catalogsDir;

    /**
     * @var string
     */
    private $ftpDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Service\ConversorManager
     */
    private $conversorManager;

    /**
     * CatalogManager constructor.
     * @param string $catalogsDir
     * @param string $ftpDir
     * @param LoggerInterface|null $logger
     * @param EntityManagerInterface $entityManager
     * @param \App\Service\ConversorManager $conversorManager
     */
    public function __construct(string $catalogsDir, string $ftpDir, LoggerInterface $logger = null, EntityManagerInterface $entityManager, ConversorManager $conversorManager)
    {
        $this->catalogsDir = $catalogsDir;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->conversorManager = $conversorManager;
        $this->ftpDir = $ftpDir;
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
                    //create product
                    $product = new Product();

                    if (!empty($row['styleNumber'])) {
                        $product->setStyleNumber($row['styleNumber']);
                    }

                    if (!empty($row['name'])) {
                        $product->setName($row['name']);
                    }

                    if (!empty($row['price'])) {
                        //create price
                        $price = new Price();

                        if (!empty($row['price']['currency'])) {
                            $price->setCurrency($row['price']['currency']);
                        }

                        if (!empty($row['price']['amount'])) {
                            $price->setAmount($row['price']['amount']);
                        }

                        //set price to created product
                        $product->setPrice($price);

                        //If product have a different currency than USD convert it
                        if (!empty($row['price']['amount']) && !empty($row['price']['currency'])) {
                            if ($price->getCurrency() != 'USD') {
                                $price->setAmount($this->conversorManager->convertProductPrice($product, 'USD'));
                                $price->setCurrency('USD');
                            }
                        }
                    }

                    if (!empty($row['images'])) {
                        $product->setImages($row['images']);
                    }

                    //add new product to the importing catalog
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
            //check if directory to store csv files is created, if not create it
            if (!file_exists($this->ftpDir)) {
                mkdir($this->ftpDir, 0777);
            }

            //create blank csv file in write mode
            $handle = fopen($this->ftpDir.'/'.$catalog->getId().'.csv', 'w');

            // Find all catalog products to export
            $products = $catalog->getProducts();

            $csv = "";

            // Iterate products
            foreach ($products as $product) {
                $csv.= implode(",", array_values($product->toArray()))."\n"; // The fields to be displayed
            }

            //write data to file
            fwrite($handle, $csv);

            //close handle
            fclose($handle);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('An Exception has ocurred: '. $e->getMessage(), ['catalog' => $catalog->getId(), 'state' => $catalog->getState()]);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCatalogsDir()
    {
        return $this->catalogsDir;
    }

    /**
     * @return string
     */
    public function getFtpDir()
    {
        return $this->ftpDir;
    }
}
