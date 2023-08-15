<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface; 
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailUploaderService
{
    private $entityManager;
    private $validator;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,MailerService $mailer  )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->mailer = $mailer;

    }

    public function uploadEmails(UploadedFile $file, Category $category)
    {
        $encoder = new CsvEncoder();
        $data = $encoder->decode(file_get_contents($file->getPathname()), 'csv');
        foreach ($data as $row) {
            $value = array_values($row);
            $email = $value[0];

            $this->saveEmailToDatabase($email, $category);
        }
    }

    private function saveEmailToDatabase(string $email, Category $category)
    {
        $emailConstraint = new EmailConstraint();
        $errors = $this->validator->validate($email, $emailConstraint);

        if (count($errors) > 0) {
            $errorMessage = (string) $errors->get(0)->getMessage();
           print( 'Invalid email address: ' . $errorMessage);
           return;
           
        }

        $user = new User();
        $user->setEmail($email);
        $user->addCategory($category);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailer->sendEmail(
            to: $user->getEmail(),
            subject: 'Votre inscription à la newsletter',
            content: 'test test',
            htmlTemplate: 'newsletter/inscription.html.twig',
            context: compact('user')
        );
        $categories=$user->getCategories();
        foreach($categories as $category){
            $newsletters=$category->getNewsletters();
            foreach($newsletters as $newsletter){
            $newsletter->setIsSent(false);
        }
        }
    }
    
}
