<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use App\Repository\BookRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\QueryBuilder;

class RentBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $date = new \DateTime();
        $maxDate = (new \DateTime())->modify('+14 days'); // Maximum 14 jours d'emprunt

        $builder
            ->add('rentFrom', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'min' => $date->format('Y-m-d'),
                    'max' => $maxDate->format('Y-m-d'),
                    'class' => 'form-control'
                ]
            ])
            ->add('rentTo', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'min' => $date->modify('+1 day')->format('Y-m-d'),
                    'max' => $maxDate->format('Y-m-d'),
                    'class' => 'form-control'
                ]
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'query_builder' => function (BookRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('b')
                        ->where('b.quantity > 0');
                },
                'choice_label' => 'title',
                'label' => 'Livre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Emprunter',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rental::class,
        ]);
    }
}
