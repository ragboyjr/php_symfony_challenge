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
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        parent::setUp();
    }

    /**
     * Test success when attaching a valid file (< max limit 50k)
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testNewSuccess tests/Controller/CatalogControllerTest.php
     * @endcode
     *
     */
    public function testNewSuccess()
    {
        $path = 'tests/catalogSuccess.json';

        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$path,
            $path,
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

    /**
     * Test success when attaching a valid long file (< max limit 50k)
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testNewSuccessLongFile tests/Controller/CatalogControllerTest.php
     * @endcode
     */
    public function testNewSuccessLongFile()
    {
        $path = 'tests/catalogSuccessLongFile.json';

        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$path,
            $path,
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

    /**
     * Test success when attaching a valid long file (= max limit 50k)
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testNewSuccessExtremeLongFile tests/Controller/CatalogControllerTest.php
     * @endcode
     */
    public function testNewSuccessExtremeLongFile()
    {
        $path = 'tests/catalogSuccessExtremeLongFile.json';

        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$path,
            $path,
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


    /**
     * Test failure when attaching an invalid long file (> max limit 50k)
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testNewFailureTooLongFile tests/Controller/CatalogControllerTest.php
     * @endcode
     */
    public function testNewFailureTooLongFile()
    {
        $path = 'tests/catalogFailureTooLongFile.json';

        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$path,
            $path,
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
        $this->assertStringContainsString('The file is too large', $this->client->getResponse()->getContent());
    }

    /**
     * Test failure when attaching an invalid file (wrong data)
     *
     * To run the testcase:
     * @code
     * ./bin/phpunit --filter testNewFailureInvalidData tests/Controller/CatalogControllerTest.php
     * @endcode
     */
    public function testNewFailureInvalidData()
    {
        $path = 'tests/catalogFailureInvalidData.json';

        $file = new UploadedFile(
            $this->client->getContainer()->getParameter('catalogs_dir').'/'.$path,
            $path,
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
        sleep(5); //wait for the messenger to handle and sync the file (or not)
        $lastInsertedCatalog = $this->entityManager->getRepository('App:Catalog')->findOneBy(
            array(),
            array('id' => 'DESC')
        );

        $this->assertInstanceOf(Catalog::class, $lastInsertedCatalog);

        //cannot handle file because invalid data, stay at submitted state
        $this->assertEquals(Catalog::SUBMITTED_STATE, $lastInsertedCatalog->getState());
    }
}
