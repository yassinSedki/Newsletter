<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FilesFunctionsTest extends WebTestCase
{
    public function testExportEmails(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/exportEmails');
        
        // to  Check if the response is successful
        $this->assertTrue($client->getResponse()->isSuccessful());

        //to Check if the response content type is CSV
        $this->assertEquals('text/csv', $client->getResponse()->headers->get('content-type'));


        //to Check if the content is downloadable as a CSV file
        $this->assertEquals(
            'attachment; filename="emails.csv"',
            $client->getResponse()->headers->get('content-disposition')
        );
    }
}