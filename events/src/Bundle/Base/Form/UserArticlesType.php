<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Method;
use App\Bundle\Base\Entity\Modality;
use App\Bundle\Base\Entity\Theory;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\ModalityRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserArticlesType
 *
 * @package App\Bundle\Base\Form
 */
class UserArticlesType extends AbstractType
{
    /**
     * @var int
     */
    public static $step = 1;

    /**
     * @var UserThemesService
     */
    private $userThemesService;

    /**
     * @param UserThemesService $userThemesService
     */
    public function __construct(UserThemesService $userThemesService)
    {
        $this->userThemesService = $userThemesService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('divisionId', EntityType::class, [
                'class' => Division::class,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'query_builder' => function (DivisionRepository $er) use ($builder) {
                    /** @var $entity UserArticles */
                    $entity = $builder->getData();

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->addOrderBy($er->replaceFieldAlias('id'), 'ASC');

                    if (count($entity->getEditionId()->getEvent()->getDivisions()) > 0) {
                        $ids = [];
                        foreach ($entity->getEditionId()->getEvent()->getDivisions() as $item) {
                            $ids[] = $item->getId();
                        }
                        $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('id'), $ids));
                    }

                    return $qb;
                },
            ])
            ->add('lastId', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => UserArticles::ARTICLE_RESULTING_FROM,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('userThemes', EntityType::class, [
                'class' => UserThemes::class,
                'query_builder' => function (UserThemesRepository $er) use ($builder, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));

                    if (! empty($data['division'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['division']));
                    }

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('division'), $ids));
                        }
                    }

                    $qb->addOrderBy('ut.position', 'ASC');
                    $qb->addOrderBy('utd.portugueseTitle', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%d - %s', $entity->getPosition(), $entity->getDetails()->getTitle());
                },
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('methodId', EntityType::class, [
                'class' => Method::class,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('language', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => UserArticles::LANGUAGES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('premium', CheckboxType::class)
            ->add('portuguese', CheckboxType::class)
            ->add('english', CheckboxType::class)
            ->add('spanish', CheckboxType::class)
            ->add('resume', TextareaType::class, [
                'required' => true,
                'constraints' => self::$step > 1 ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('acknowledgment', TextareaType::class)
            ->add('frame', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choice_loader' => new CallbackChoiceLoader(function () use ($builder) {
                    $entity = $builder->getData();

                    $event = strtoupper($entity->getEditionId()->getEvent()->getNamePortuguese());

                    $value = in_array($event, ['ENANPAD', 'ENEPQ']) ? UserArticles::FRAMES : UserArticles::FRAMES_DIVISIONAL;

                    return $value;
                }),
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],

            ])
            ->add('jobComplete', RadioType::class, [
                //'data' => true,
            ])
            ->add('resumeFlag', RadioType::class)
            ->add('neverPublish', CheckboxType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('isRelatedThemeEnanpad', CheckboxType::class)
            ->add('userArticlesFiles', CollectionType::class, [
                'entry_type' => UserArticlesFilesType::class,
                'required' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('userArticlesAuthors', CollectionType::class, [
                'entry_type' => UserArticlesAuthorsType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'max' => 6,
                    ]),
                ],
                'error_bubbling' => false,
            ]);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserArticles::class,
            'edition' => null,
            'validation_groups' => function (FormInterface $form) {
                $groups = ['Default'];
                $data = $form->getData();

                if ($data->getResume()) {
                    $groups[] = 'SOME_OTHER_VALIDATION_GROUP';
                }

                return $groups;
            },
        ]);
    }
}
