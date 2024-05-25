<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3'
                ],
                'required' => true
            ])
            ->add('newPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3'
                ],
                'required' => true
            ])
            ->add('confirmPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md mb-3'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
