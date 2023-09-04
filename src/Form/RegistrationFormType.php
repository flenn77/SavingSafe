<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                    new Length(['min' => 2, 'max' => 50]),
                    new Regex(['pattern' => '/^[a-zA-Z\s\-]+$/', 'message' => 'Seuls les lettres, les espaces et les tirets "-" sont autorisés.']),
                ]
            ])
            ->add('last_name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                    new Length(['min' => 2, 'max' => 50]),
                    new Regex(['pattern' => '/^[a-zA-Z\s\-]+$/', 'message' => 'Seuls les lettres, les espaces et les tirets "-" sont autorisés.']),
                ]
            ])
            ->add('phone_number', TelType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne peut pas être vide.']),
                    new Length(['min' => 10, 'max' => 18]),
                    new Regex(['pattern' => '/^\+?[0-9]+$/', 'message' => 'Le numéro de téléphone doit être composé uniquement de chiffres et peut commencer par un +.']),
                ]
            ])
            ->add('adress', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                    new Length(['min' => 5, 'max' => 255]),
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville ne peut pas être vide.']),
                    new Length(['min' => 3, 'max' => 50]),
                    new Regex(['pattern' => '/^[a-zA-Z\s]+$/', 'message' => 'Seuls les lettres et les espaces sont autorisés.']),
                ]
            ])
            ->add('postal_code', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal ne peut pas être vide.']),
                    new Length(['min' => 5, 'max' => 10]),
                    new Regex(['pattern' => '/^\d+$/', 'message' => 'Seuls les chiffres sont autorisés.']),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse e-mail ne peut pas être vide.']),
                    new Email(['mode' => 'strict']),
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length(['min' => 8, 'max' => 200, 'minMessage' => 'Votre mot de passe doit comporter au moins 8 caractères']),
                    new Regex(['pattern' => '/^(?=.*[a-zA-Z])(?=.*\d).+$/', 'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
