<?php

namespace App\Form;

use App\Entity\Historia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Seleccione la foto',
                'mapped' => false,
                'required' => false,
                'attr' => ['id' => 'postInput'], // Asignando el id al input de tipo file
            ])
            ->add('compartir', SubmitType::class,[
                'label' => 'Compartir'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Historia::class,
        ]);
    }
}
