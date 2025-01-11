<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookRead;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LectureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // creation different attente pour le form d'ajout de livre
        $builder->add('book_id', EntityType::class, [
            'required' => true,
            'class' => Book::class,
            'choice_label' => 'name',
            'choice_value' => 'id',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Notez ici les idées importantes de l\'œuvre.'],
            ])
            ->add('rating', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '1.5' => 1.5,
                    '2' => 2,
                    '2.5' => 2.5,
                    '3' => 3,
                    '3.5' => 3.5,
                    '4' => 4,
                    '4.5' => 4.5,
                    '5' => 5,
                ],
            ])
            ->add('is_read', CheckboxType::class, [
                'required' => false,
                'label' => 'Lecture terminée',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookRead::class,
        ]);
    }
}
