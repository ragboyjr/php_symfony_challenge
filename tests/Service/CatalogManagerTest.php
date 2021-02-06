<?php

namespace App\Tests\Service;

use App\Entity\Catalog;
use App\Service\CatalogManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CatalogManagerTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var CatalogManager
     */
    private $catalogManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->catalogManager = $kernel->getContainer()
            ->get('catalog_manager');

        parent::setUp();
    }

    /**
     * Test testHandle method
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testHandle tests/Service/CatalogManagerTest.php
     * @endcode
     *
     */
    public function testHandle()
    {
        $catalog = new Catalog();
        $catalog->setFilePath('tests/catalogSuccess.json');

        //test handle method
        $answer = $this->catalogManager->handle($catalog);

        //assert
        $this->assertTrue($answer);
        $this->assertGreaterThan(0, count($catalog->getProducts()));
    }

    /**
     * Test testExport method
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testExport tests/Service/CatalogManagerTest.php
     * @endcode
     *
     */
    public function testExport()
    {
        $catalog = new Catalog();
        $catalog->setFilePath('catalogSuccess.json');
        
        //test export method
        $answer = $this->catalogManager->export($catalog);

        //assert
        $this->assertTrue($answer);
        $this->assertFileExists($this->catalogManager->getFtpDir().'/'.$catalog->getId().'.csv');

        //unlink generated csv
        if (file_exists($this->catalogManager->getFtpDir().'/'.$catalog->getId().'.csv')) {
            unlink($this->catalogManager->getFtpDir().'/'.$catalog->getId().'.csv');
        }
    }
}
