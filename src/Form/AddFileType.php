<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File as ConFile;

class AddFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Déposez vos fichiers ici ou cliquez',
                    'class' => 'dropzone-media'
                ],
                'constraints' => [
                    new ConFile([
                        'maxSize' => '250M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                    ],
                'translation_domain' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
