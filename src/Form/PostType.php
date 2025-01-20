<?php

namespace App\Form;

use App\Entity\Postes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Post:',
                'attr' => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image:',
                'attr' => ['class' => 'form-control'],
                'mapped' => false, // This field is not mapped to an entity property
                'required' => false, // Make the image optional
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Postes::class,
        ]);
    }
}
