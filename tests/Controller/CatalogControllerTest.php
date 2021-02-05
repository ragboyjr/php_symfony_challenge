<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->uploadedFile = $kernel->getContainer()->getParameter('catalogs_dir').'/catalog1.json';

        parent::setUp();
    }


    public function testImport()
    {
        $_FILES = [
            'filename' => [
                'name' => $this->uploadedFile,
                'type' => 'text/plain',
                'size' => 211,
                'tmp_name' => $this->uploadedFile,
                'error' => 0
            ]
        ];

        //do call and get response and client
        list($response, $client) = $this->call(
            'POST',
            '/?entity=Catalog&action=list',
            [],
            $_FILES,
            []
        );

        var_dump($client->getResponse()->getStatusCode());
        die();
    }
}
