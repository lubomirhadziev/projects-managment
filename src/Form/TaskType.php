<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'New' => User::STATUS_NEW,
                    'Pending' => User::STATUS_PENDING,
                    'Failed' => User::STATUS_FAILED,
                    'Done' => User::STATUS_DONE,
                ],
            ])
            ->add('duration', IntegerType::class)
            ->add('save', SubmitType::class);
    }

}