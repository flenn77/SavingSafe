<?php

namespace App\Form;

use App\Model\ChangePasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre ancien mot de passe.']),
                    new Length(['min' => 6, 'max' => 200, 'minMessage' => 'Le mot de passe doit contenir au moins 6 caractères.']),
                ]
            ])
            ->add('new_password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un nouveau mot de passe.']),
                    new Length(['min' => 8, 'max' => 200, 'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.']),
                    new Regex([
                        'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.'
                    ]),
                ]
            ])
            ->add('confirm_new_password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez confirmer votre nouveau mot de passe.']),
                    new Length(['min' => 8, 'max' => 200, 'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.']),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePasswordModel::class,
        ]);
    }
}
