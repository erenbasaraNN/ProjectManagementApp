<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate', null, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => ['class' => 'js-datepicker'],
                'input_format' => 'Y-m-d',

            ])
            ->add('endDate', null, [
                'widget' => 'single_text',
            ])
            ->add('creator', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
