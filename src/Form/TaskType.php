<?php

// src/Form/TaskType.php
namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Task::STATUS_OPTIONS),
                'label' => 'Status',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name', // This ensures the project name is displayed
                'label' => 'Project',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
