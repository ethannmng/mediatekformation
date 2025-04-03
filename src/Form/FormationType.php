<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', null, [
                'label' => "Date de publication",
                'widget' => 'single_text',
                'attr' => [
                    'type' => 'date',
                    'onkeydown' => 'return false;',
                    'max' => (new \DateTime())->format('Y-m-d')
                ]
            ])
            ->add('title', null, ['label' => "Titre de la formation"])
            ->add('description', null, [
                'label' => "Description"
                ]) 
            ->add('videoId', null, [
                'label' => "ID de la vidéo"
                ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'label' => "Playlist",
                'choice_label' => 'name',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'label' => "Catégories",
                'choice_label' => "name",
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
