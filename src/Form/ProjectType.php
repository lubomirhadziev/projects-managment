<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProjectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'constraints' => [
                new Callback([$this, 'validate']),
            ],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('client', TextType::class, [
                'required' => false
            ])
            ->add('company', TextType::class, [
                'required' => false
            ])
            ->add('save', SubmitType::class);
    }

    /**
     * @param Project $data
     * @param ExecutionContextInterface $context
     */
    public function validate(Project $data, ExecutionContextInterface $context): void
    {
        if (empty($data->getClient()) && empty($data->getCompany())) {
            $context->buildViolation('Client or Company must to be filled!')
                ->addViolation();
        }
    }

}