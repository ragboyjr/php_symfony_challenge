<?php

namespace App\Tests\Controller;

use App\Entity\Catalog;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class CatalogControllerTest
 * @package App\Tests\Controller
 *
 * To run the testcase:
 * @code
 * ./bin/phpunit tests/Controller/CatalogControllerTest.php
 * @endcode
 */
class CatalogControllerTest extends WebTestCase
{
    private $uploadedFile;

    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->uploadedFile = 'catalog1.json';

        parent::setUp();
    }


    public function testNew()
    {
        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$this->uploadedFile,
            $this->uploadedFile,
            'test/plain'
        );

        //go to catalog new view
        $crawler = $this->client->request(
            'GET',
            '/?entity=Catalog&action=new'
        );

        //fill form
        $form = $crawler->filter('#new-catalog-form')->form();
        $form['catalog[file][file]'] = $file; //attach file
        $this->client->submit($form); // submit the form

        //Asserts
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $lastInsertedCatalog = $this->entityManager->getRepository('App:Catalog')->findOneBy(
            array(),
            array('id' => 'DESC')
        );

        $this->assertInstanceOf(Catalog::class, $lastInsertedCatalog);
        $this->assertFileExists($this->client->getContainer()->getParameter('catalogs_dir').'/'.$lastInsertedCatalog->getFilePath());
    }
}
