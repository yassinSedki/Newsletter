<?php
namespace App\Tests;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CategoryTest extends KernelTestCase
{
    public function getCategory(){
        return (new Category())->setName('test');
    }
    public function testCategoryIsValid(): void
    {
         self::bootKernel();

        $container= static::getContainer();
       
        $errors = $container->get('validator')->validate($this->getCategory());
        $this->assertCount(0,$errors);
    }
}