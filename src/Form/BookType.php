<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'book title'])
            ->add('author', TextType::class, ['label' => 'book author'])
            ->add(
                'description',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'short description of the book'
                ]
            )
            ->add('yearOfPublication', IntegerType::class, ['label' => 'year of publication'])


            //todo handle isbn 10 and 13 char


            ->add('isbn', TextType::class)
            ->add(
                'coverFileName',
                FileType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new Image(['maxSize' => '1024k'])
                    ]
                ]
            )
            ->add('submit', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
