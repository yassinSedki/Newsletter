<?php

namespace App\Tests;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    public function getUser(){
        return (new User())->setEmail('userTesting@gmail.com')
        ->setIsValid(true);
    }
    public function testUserIsValid(): void
    {
         self::bootKernel();

        $container= static::getContainer();
       
        $errors = $container->get('validator')->validate($this->getUser());
        $this->assertCount(0,$errors);
    }
    private ValidatorInterface $validator;

    public function testEmailCannotBeEmpty()
    {
        self::bootKernel();
        $container= static::getContainer();

        $this->validator = $container->get('validator');
        $user = new User();
        $user->setEmail('');

        $violations = $this->validator->validate($user);

        $this->assertCount(0, $violations);
     
    }
}