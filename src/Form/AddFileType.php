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
                    'class' => 'dropzone-media',
                ],
                'constraints' => [
                    new ConFile([
                        'maxSize' => '250M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/bmp',
                            'image/webp',
                            'image/svg+xml',
                            'audio/mpeg',
                            'audio/wav',
                            'audio/ogg',
                            'video/mp4',
                            'video/x-msvideo',
                            'video/quicktime',
                            'video/x-flv',
                            'video/ogg',
                            'video/webm',
                            'application/pdf',
                            'application/x-pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'application/zip',
                            'application/x-rar-compressed',
                            'application/x-tar',
                            'application/x-7z-compressed',
                            'text/plain',
                            'text/html',
                            'text/css',
                            'text/javascript',
                            'application/octet-stream',
                            'application/json',
                            'application/xml',
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
