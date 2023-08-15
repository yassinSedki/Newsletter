<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\EmailTracking;
use App\Entity\Newsletter;
use App\Entity\User;
use App\Form\CategoryType; 
use App\Form\NewsletterType;
use App\Form\UserType;
use App\Repository\NewsletterRepository;
use App\Repository\UserRepository;
use App\Service\EmailerViewerService;
use App\Service\EmailTrackingService;
use App\Service\EmailUploaderService;
use App\Service\MailerService;
use Doctrine\Persistence\ManagerRegistry;
use LMammino\Http\PixelResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NewsletterController extends AbstractController
{
    private $emailerViewer;

    public function __construct(EmailTrackingService $emailerViewer)
    {
        $this->emailerViewer = $emailerViewer;
    }
    #[Route('/addCategory', name: 'add.category')]
    public function addCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', "Category added successfully");
        } 
        
        return $this->render('newsletter/add-category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/addUser', name: 'add.user')]
    
    public function addUser(Request $request, ManagerRegistry $doctrine, MailerService $mailer): Response
    {
        $user = new User();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $form = $this->createForm(UserType::class, $user, [
            'categories' => $categories,
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $token = password_hash(uniqid(), PASSWORD_DEFAULT);
            $user->setToken($token);
            $manager = $doctrine->getManager();
            $manager->persist($user);
            $manager->flush();
    
            $htmlTemplate = 'newsletter/inscription.html.twig';
    
            // Corrected the sendEmail call with named arguments.
            $mailer->sendEmail(
                to: $user->getEmail(),
                subject: 'Votre inscription à la newsletter',
                content: 'test test',
                htmlTemplate: $htmlTemplate,
                context: compact('user', 'token')
            );
    
            $this->addFlash('success', "waiting for validation....");
    
            return $this->render('newsletter/add-user.html.twig', [
                'id' => $user->getId(),
                'token' => $user->getToken(),
                'form' => $form->createView()
                
            ]);
        }
    
        return $this->render('newsletter/add-user.html.twig', [
            'form' => $form->createView()
        ]);
    }
    


    //#[Route('/confirm/{id}/{token}',name:'confirmation') ]
    #[Route('/confirm/{id}',name:'confirmation') ]
    public function confirmMail(User $user, ManagerRegistry $doctrine): Response
    {
     
        $user->setIsValid(true);
        $categories=$user->getCategories();
        foreach($categories as $category){
            $newsletters=$category->getNewsletters();
            foreach($newsletters as $newsletter){
            $newsletter->setIsSent(false);
        }
        }
        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush();
        $this->addFlash('success', "Mail confirmed");
        return $this->redirectToRoute('list.newsletters');
    }


    #[Route('/addNewsletter', name: 'add.newsletter')]
    public function addNewsletter(Request $request, ManagerRegistry $doctrine):Response{
        $entityManager = $doctrine->getManager();
        $newsletter = new Newsletter();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $form = $this->createForm(NewsletterType::class, $newsletter, [
        'category' => $categories,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($newsletter);
            $entityManager->flush();
            return $this->redirectToRoute('list.newsletters');
        }
        return $this->render('newsletter/add-newsletter.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    
        #[Route('/newsletterList',name:'list.newsletters')]
        public function newsletterList(NewsletterRepository $newsletters):Response{
            
            return $this->render('newsletter/list-newsletter.html.twig',[
                'newsletters'=>$newsletters->findAll()
            ]);
        } 

        #[Route('/send/{id}', name: 'send')]
        public function send(Newsletter $newsletter, MailerService $mailer): Response
        {
            $categories = $newsletter->getCategory();
            foreach($categories as $category){
            $users= $category->getUsers();
            $htmlTemplate = 'newsletter/email.html.twig';
            foreach ($users as $user) {
                if ($user->isIsValid()) {
                    $context = [
                        'newsletter' => $newsletter,
                        'user' => $user,
                        
                    ];
                    $mailer->sendEmail(
                        to: $user->getEmail(),
                        subject: $newsletter->getName(),
                        content: $newsletter->getContent(),
                        htmlTemplate: $htmlTemplate,
                        context: $context
                    );
                }
            }
        }
            return $this->redirectToRoute('list.newsletters');
        }
        #[Route('/send_to_all/{newsletter}', name: 'send_to_all')]
    public function sendToAll(Newsletter $newsletter, MailerService $mailer,ManagerRegistry $doctrine): Response
    {

        foreach ($newsletter->getCategory() as $category) {
            foreach ($category->getUsers() as $user) {
                if ($user->isIsValid()) {
                    $context = [
                        'newsletter' => $newsletter,
                        'user' => $user,

                    ];
                    $mailer->sendEmail(
                        to: $user->getEmail(),
                        subject: $newsletter->getName(),
                        content: $newsletter->getContent(),
                        htmlTemplate: 'newsletter/email.html.twig',
                        context: $context
                    );
                }
            }
        }

        $newsletter->setIsSent(true);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($newsletter);
        $entityManager->flush();
        $this->addFlash('success','emails sent successfully');
        return $this->redirectToRoute('list.newsletters');
    }
    #[Route('/usersList/{startPage?1}/{numberOfElements?45}',name:'list.users')]
        public function userList(UserRepository $users,$startPage,$numberOfElements):Response{
            $nbUsers=$users->count([]);
            $nbrePages=ceil($nbUsers/$numberOfElements);

            $selectedUsers=$users->findBy([],[],$numberOfElements,$startPage-1);
            return $this->render('newsletter/list-user.html.twig',[
                'users'=>$selectedUsers,
                'nbrePages'=>$nbrePages,
                'page'=>$startPage,
                'nmbre'=>$numberOfElements
           
            ]);
        } 
    #[Route('/exportEmails',name:'export.emails')]
    public function exportEmails(UserRepository $userRepository){
        $selectedEmails = $userRepository->createQueryBuilder('u')
            ->select('u.email')
            ->getQuery()
            ->getArrayResult();
        $emails=   array_column($selectedEmails, 'email');
        
        $response = new StreamedResponse(function () use ($emails) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email']); 

            foreach ($emails as $email) {
                fputcsv($handle, [$email]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'emails.csv'
        ));
        return $response;
    }
    #[Route('/uploadEmails',name:'upload.emails')]
    public function uploadEmails(Request $request,EmailUploaderService $emailUploader)
    {
        $form = $this->createFormBuilder()
        ->add('file', FileType::class, [
            'label' => 'Choose a CSV file',
            'mapped' => false, 
        ])
        
        ->add('category', EntityType::class, [
            'class' => Category::class, 
            'choice_label' => 'name', 
            'placeholder' => 'Select a category', 
            'required' => false, 
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Upload',
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            $category = $form->get('category')->getData();
            $emailUploader->uploadEmails($uploadedFile,$category);
            

          
            $this->addFlash('success', 'Email addresses uploaded successfully.');
        }
        

        return $this->render('newsletter/add-csv.html.twig', [
            'form' => $form->createView(),
        ]);
    }
     
         
    #[Route('/pixel.png', name: 'track_email')]
    public function trackEmailView(EmailTrackingService $emailTrackingService): Response
    {
        $imageUrl = 'http://127.0.0.1:8000/images/pixel.png';
        $trackingId = uniqid();

        $emailTrackingService->trackEmailView($imageUrl, $trackingId);

        $response = new Response(file_get_contents($imageUrl));
        $response->headers->set('Content-Type', 'image/gif');

        return $response;
    }



    #[Route('/viewed-percentage', name: 'viewed_percentage')]
    public function viewedPercentage(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        // Get the total number of sent emails
        $totalSentEmails = $entityManager->getRepository(EmailTracking::class)->count([]);
        
        // Get the number of viewed emails
        $viewedEmails = $entityManager->getRepository(EmailTracking::class)->count(['viewed' => true]);

        // Calculate the percentage
        $percentage = ($totalSentEmails > 0) ? ($viewedEmails / $totalSentEmails) * 100 : 0;

        return $this->render('newsletter/viewed-percentage.html.twig', [
            'percentage' => $percentage,
            'totalSentEmails' => $totalSentEmails,
            'viewedEmails' => $viewedEmails,
        ]);
    }
}

    
    
    
