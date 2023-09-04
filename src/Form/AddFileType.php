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
                        'maxSize' => '25000M',
                        'mimeTypes' => [
                            // Documents
                            'application/pdf',
                            'application/x-pdf',
                            'application/msword', // DOC
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // DOCX
                            'application/vnd.ms-excel', // XLS
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // XLSX
                            'application/vnd.ms-powerpoint', // PPT
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PPTX
                            'text/plain', // TXT

                            // Images
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/bmp',
                            'image/webp',
                            'image/svg+xml',
                            'image/tiff',
                            'image/bmp',

                            // Audio
                            'audio/mpeg', // MP3
                            'audio/wav',

                            // Video
                            'video/mp4',
                            'video/x-msvideo', // AVI
                            'video/quicktime', // MOV

                            // Archives
                            'application/zip',
                            'application/x-rar-compressed',
                            'application/x-tar',
                            'application/x-7z-compressed',

                            // Others
                            'application/octet-stream',
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
