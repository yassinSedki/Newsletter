<?php

namespace App\Form;

use App\Entity\Newsletter;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', null, [ 
            'constraints' => [
                new NotBlank([
                    'message' => 'Please provide a name for the newsletter.', 
                ]),
            ],
        ])
        ->add('content', null, [ 
            'constraints' => [
                new NotBlank([
                    'message' => 'Please provide a content for the newsletter.', 
                ]),
            ],
        ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('send', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class, 
            'category' => [],
        ]);
    }
}
