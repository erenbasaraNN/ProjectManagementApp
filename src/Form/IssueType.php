<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate', null, [
                'widget' => 'single_text',
            ])
            ->add('endDate', null, [
                'widget' => 'single_text',
            ])
            ->add('status')
            ->add('priority')
            ->add('assignees', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name', // Choose how to display the user (e.g., by name)
                'multiple' => true, // Allow multiple users to be selected
                'expanded' => true, // Use checkboxes (optional)
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
            ])
            ->add('tags', TextType::class, [
                'mapped' => false, // Etiketleri manuel olarak işleyeceğiz
                'attr' => [
                    'class' => 'tagify',    // Tagify'ın uygulanacağı input
                    'name' => 'tags'        // Aynı JS dosyasında seçtiğimiz name
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }
}
