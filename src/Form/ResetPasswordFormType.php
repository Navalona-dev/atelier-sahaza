<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control form-control-md mb-3'
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le mot de passe ne doit pas être null',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit être minimum 8 caractères',
                            'max' => 4096,
                        ]),
                        new Assert\Regex([
                            'pattern' => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).*$/",
                            'message' => "Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre, un caractère non alphanumérique."
                        ]),
                        new Assert\Regex([
                            'pattern' => "/^(?!.*(\w)\1).{8,}$/",
                            'message' => "Le mot de passe ne doit pas contenir 3 caractères consécutifs ou plus."
                        ]),
                    ],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                ],
                'invalid_message' => 'La confirmation de mot de passe doit être confondre que le mot de passe',
                'mapped' => false,
                'row_attr' => [
                    'class' => 'password-div', // Ajout d'une classe CSS pour le div
                ],
            ]);

    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
