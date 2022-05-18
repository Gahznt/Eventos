<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserThemesType extends AbstractType
{
    const USER_THEMES_BIBLIOGRAPHIES_COUNT_MIN = 5;
    const USER_THEMES_BIBLIOGRAPHIES_COUNT_MAX = 10;
    const USER_THEMES_BIBLIOGRAPHIES_UNIQUE_MESSAGE = 'Esta bibliografia de referência para o tema já foi indicada anteriormente';

    const USER_THEMES_RESEARCHERS_COUNT_MIN = 2;
    const USER_THEMES_RESEARCHERS_COUNT_MAX = 3;
    const USER_THEMES_RESEARCHERS_UNIQUE_MESSAGE = 'Este proponente já foi indicado na equipe anteriormente';

    const USER_THEMES_REVIEWERS_COUNT_MIN = 0;
    const USER_THEMES_REVIEWERS_COUNT_MAX = 6;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('details', UserThemesDetailsType::class, [
                'data_class' => UserThemesDetails::class,
                'label' => false,
                'error_bubbling' => false,
            ])
            ->add('userThemesBibliographies', CollectionType::class, [
                'entry_type' => UserThemesBibliographiesType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => self::USER_THEMES_BIBLIOGRAPHIES_COUNT_MIN,
                        'max' => self::USER_THEMES_BIBLIOGRAPHIES_COUNT_MAX,
                        'groups' => ['theme-submission-step-1'],
                    ]),
                ],
                'error_bubbling' => false,
            ])
            ->add('userThemesResearchers', CollectionType::class, [
                'entry_type' => UserThemesResearchersType::class,
                'entry_options' => [
                    'label' => false,
                    'researchers' => $options['researchers'],
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => self::USER_THEMES_RESEARCHERS_COUNT_MIN,
                        'max' => self::USER_THEMES_RESEARCHERS_COUNT_MAX,
                        'groups' => ['theme-submission-step-2'],
                    ]),
                ],
                'error_bubbling' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserThemes::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => function (FormInterface $form) {
                $step = (int)$form->getConfig()->getOption('step', 1);

                $groups = [];

                for ($i = 1; $i <= $step; $i++) {
                    $groups[] = sprintf('theme-submission-step-%d', $i);
                }

                return $groups;
            },
            'step' => 1,
            'researchers' => [],
        ]);
    }
}
