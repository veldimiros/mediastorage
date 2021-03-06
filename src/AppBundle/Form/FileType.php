<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType as File;

class FileType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('info', TextareaType::class, [
                    'label' => false,
                    'attr' => [
                        'autofocus' => true,
                        'placeholder' => 'Please enter here the description of the downloaded file',
                        'maxlength' => 150,
                        'style' => 'resize: none'
                    ]
                ])
                ->add('file', File::class)
                ->add('email', EmailType::class, [
                    'attr' => [
                        'placeholder' => 'Please enter here Email',
                        'maxlength' => 150,
                    ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\File'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_file';
    }

}
