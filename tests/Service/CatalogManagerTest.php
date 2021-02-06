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
     * @var Catalog
     */
    private $catalog;

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

        $this->catalog = new Catalog();
        $this->catalog->setFilePath('catalog1.json');
        $this->entityManager->persist($this->catalog);
        $this->entityManager->flush();

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
        //test handle method
        $answer = $this->catalogManager->handle($this->catalog);

        //assert
        $this->assertTrue($answer);
        $this->assertGreaterThan(0, count($this->catalog->getProducts()));
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
        //test export method
        $answer = $this->catalogManager->export($this->catalog);

        //assert
        $this->assertTrue($answer);
        $this->assertFileExists($this->catalogManager->getFtpDir().'/'.$this->catalog->getId().'.csv');
    }

    public function tearDown(): void
    {
        $this->entityManager->clear();

        //unlink generated csv
        if (file_exists($this->catalogManager->getFtpDir().'/'.$this->catalog->getId().'.csv')) {
            unlink($this->catalogManager->getFtpDir().'/'.$this->catalog->getId().'.csv');
        }

        //set file path to '' in order to use de json file for another pourposes
        $this->catalog->setFilePath('');
        $this->entityManager->persist($this->catalog);

        $this->entityManager->remove($this->catalog);
        $this->entityManager->flush();

        parent::tearDown();
    }
}
