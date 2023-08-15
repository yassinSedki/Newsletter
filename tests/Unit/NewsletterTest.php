<?php
namespace App\Tests;
use App\Entity\Category;
use App\Entity\Newsletter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class NewsletterTest extends KernelTestCase
{
    public function getNewsletter(){
        return (new Newsletter())->setName('test')
        ->setContent('testiiing')
        ->addCategory($this->getTestCategory());
    }
    public function getTestCategory()
    {
        return (new Category())->setName('series'); 
    }
    public function testNewsletterIsValid(): void
    {
         self::bootKernel();

        $container= static::getContainer();
       
        $errors = $container->get('validator')->validate($this->getNewsletter());
        $this->assertCount(0,$errors);
    }
}