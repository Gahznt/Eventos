<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\UserThemesDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserThemesDetailsType extends AbstractType
{
    const PORTUGUESE_KEYWORDS_COUNT_MIN = 3;
    const PORTUGUESE_KEYWORDS_COUNT_MAX = 5;

    const PORTUGUESE_DESCRIPTION_WORD_COUNT_MIN = 150;
    const PORTUGUESE_DESCRIPTION_WORD_COUNT_MAX = 300;

    const ENGLISH_KEYWORDS_COUNT_MIN = 3;
    const ENGLISH_KEYWORDS_COUNT_MAX = 5;

    const ENGLISH_DESCRIPTION_WORD_COUNT_MIN = 150;
    const ENGLISH_DESCRIPTION_WORD_COUNT_MAX = 300;

    const DESCRIPTION_WORD_COUNT_MIN_MESSAGE = 'A descrição do tema proposto deve ter no mínimo %min% palavras';
    const DESCRIPTION_WORD_COUNT_MAX_MESSAGE = 'A descrição do tema proposto deve ter no máximo %max% palavras';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('portugueseTitle', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
            ])
            ->add('portugueseDescription', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
            ])
            ->add('portugueseKeywords', CollectionType::class, [
                'entry_type' => TextType::class,
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => self::PORTUGUESE_KEYWORDS_COUNT_MIN,
                        'max' => self::PORTUGUESE_KEYWORDS_COUNT_MAX,
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
                'error_bubbling' => false,
            ])
            ->add('englishTitle', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
            ])
            ->add('englishDescription', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
            ])
            ->add('englishKeywords', CollectionType::class, [
                'entry_type' => TextType::class,
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => self::ENGLISH_KEYWORDS_COUNT_MIN,
                        'max' => self::ENGLISH_KEYWORDS_COUNT_MAX,
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
                'error_bubbling' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserThemesDetails::class,
            'attr' => ['novalidate' => 'novalidate', 'id' => 'themeSubmission'],
        ]);
    }
}
