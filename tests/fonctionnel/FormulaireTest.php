<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category; 
use Symfony\Component\HttpFoundation\Response;

class FormulaireTest extends WebTestCase
{

    public function testUserForm(): void
    {
        $client = static::createClient(); 

        $crawler = $client->request('GET', '/addUser');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'add user');

        $submitButton = $crawler->selectButton('user[add]');
        $form = $submitButton->form();


        $form["user[email]"] = "yassin@gmail.com";
        $form['user[categories]']=["1"];

        // Submit the form
       $client->submit($form);
    //vérifier l'envoi de mail:
    $this->assertEmailCount(1);
        
    }
    public function testCategoryForm(){
        $client=static::createClient();
        $crawler=$client->request('get','/addCategory');
        $this->assertResponseIsSuccessful();
        $submitButton=$crawler->selectButton('category[add]');
        $form=$submitButton->form();
        $form['category[name]']='anime';
        $client->submit($form);
    }
    public function testNewsletterForm(){
        $client=static::createClient();
        $crawler=$client->request('get','/addNewsletter');
        $this->assertResponseIsSuccessful();
        $submitButton=$crawler->selectButton('newsletter[send]');
        $form=$submitButton->form();
        $form['newsletter[name]']='new trailer';
        $form['newsletter[content]']='anilme new trailer to watch';
        $form['newsletter[category]']=['1'];

        $client->submit($form);
    }

   
}
