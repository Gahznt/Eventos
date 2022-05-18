<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemesResearchers;
use App\Bundle\Base\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserThemesResearchersType extends AbstractType
{
    const BIOGRAPHY_WORD_COUNT_MAX = 200;
    const IS_ASSOCIATED_PPG_COUNT_MIN = 1;
    const IS_POSTGRADUATE_PROGRAM_PROFESSOR_COUNT_MIN = 2;

    const IS_LOGGED_USER_EXISTS_MESSAGE = 'O usuário logado deve estar na lista';
    const BIOGRAPHY_WORD_COUNT_MAX_MESSAGE = 'A biografia resumida dos proponentes deve ter até %max% palavras';
    const USER_THEMES_RESEARCHERS_ONLY_ASSOCIATED_MESSAGE = 'Os proponentes devem ser filiados à ANPAD';
    const USER_THEMES_RESEARCHERS_UNIQUE_PER_INSTITUTION_MESSAGE = 'Os proponentes devem estar vinculados a IES diferentes';
    const USER_THEMES_RESEARCHERS_LIMIT_BY_THEME_MESSAGE = 'Cada proponente poderá participar de apenas uma proposta de tema';
    const IS_ASSOCIATED_PPG_COUNT_MIN_MESSAGE = 'Pelo menos um dos proponentes deve ser de PPG associado como Membro Efetivo da ANPAD';
    const IS_POSTGRADUATE_PROGRAM_PROFESSOR_COUNT_MIN_MESSAGE = 'A equipe deverá ser formada por pelo menos dois docentes de PPG';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('researcher', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'query_builder' => function (UserRepository $er) use ($builder, $options) {
                    $qb = $er->createQueryBuilder('u');

                    $qb->andWhere(
                        $qb->expr()->eq('u.id', (int)($options['researchers'][$builder->getName()]['researcher'] ?? -1))
                    );

                    $qb->setMaxResults(1);

                    return $qb;
                },
                'choice_label' => function (User $entity) {
                    return $entity->getName();
                },
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-2'],
                    ]),
                ],
            ])
            ->add('biography', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-2'],
                    ]),
                ],
            ])
            ->add('curriculumLattesLink', TextType::class)
            ->add('isPostgraduateProgramProfessor', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserThemesResearchers::class,
            'researchers' => [],
        ]);
    }
}
