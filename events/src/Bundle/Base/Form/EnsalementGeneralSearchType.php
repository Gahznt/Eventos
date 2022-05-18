<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEnsalementScheduling as Scheduling;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\SystemEnsalementSchedulingRepository as SchedulingRepository;
use App\Bundle\Base\Repository\SystemEnsalementSessionsRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EnsalementGeneralSearchType
 *
 * @package App\Bundle\Base\Form
 */
class EnsalementGeneralSearchType extends AbstractType
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return CoordinatorsSearchType
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * EnsalementGeneralSearchType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setOptions($options);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'search';
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addElements($form, $data);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     */
    protected function addElements(FormInterface $form, $data = [])
    {
        $options = $this->getOptions();

        if (! isset($data['division'])) {
            $data['division'] = '';
        }

        if (! isset($data['theme'])) {
            $data['theme'] = '';
        }

        if (! isset($data['date'])) {
            $data['date'] = '';
        }

        $form->add('division', EntityType::class, [
            'class' => Division::class,
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                // new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);

        $form->add('theme', ! empty($data['division']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'attr' => [
                    'disabled' => ! empty($data['division']) ? false : true,
                ],
                'constraints' => ! empty($data['division']) ? [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'required' => false,
                'mapped' => false,
            ] + (! empty($data['division']) ? [
                'class' => UserThemes::class,
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

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
            ] : [
                'choice_translation_domain' => 'messages',
            ]));

        $form->add('section', EntityType::class, [
            'class' => Scheduling::class,
            'query_builder' => function (EntityRepository $er) use ($form, $data) {
                if (null !== $form->getData()->getEdition()) {
                    return $er->list($form->getData()->getEdition()->getId())->orderBy('sesss.title');
                }
            },
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                // new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->em;
        $form->add('user', EntityType::class, [
            'class' => User::class,
            'query_builder' => function (UserRepository $er) use ($form, $data, $entityManager) {

                $qb = $er->createQueryBuilder($er->getAlias());

                $qb->innerJoin(Scheduling::class, 'sesss', 'WITH', 'sesss.coordinatorDebater1=u.id OR sesss.coordinatorDebater2=u.id');

                /** @var SchedulingRepository $sesss */
                $sesss = $entityManager->getRepository(Scheduling::class);

                $qb = $sesss->buildListQuery($qb, $form->getData()->getEdition()->getId());

                $qb->groupBy('u.id');

                $qb->orderBy('u.name');

                return $qb;
            },
            'choice_label' => function (User $entity) {
                return $entity->getName();
            },
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                // new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);

        $form->add('date', EntityType::class, [
            'class' => SystemEnsalementSessions::class,
            'query_builder' => function (SystemEnsalementSessionsRepository $er) use ($form) {

                $qb = $er->createQueryBuilder($er->getAlias());
                $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                if (null !== $form->getData()->getEdition()) {
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $form->getData()->getEdition()->getId()));
                }
                $qb->groupBy($er->replaceFieldAlias('date'));
                $qb->addOrderBy($er->replaceFieldAlias('date'), 'ASC');

                return $qb;
            },
            'choice_label' => function ($entity) {
                return $entity->getDate()->format('d/m/Y');
            },
            'choice_value' => function ($entity) {
                return $entity ? $entity->getDate()->format('Y-m-d') : '';
            },
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => empty($data['systemEnsalementSlots']) ? true : false,
            'constraints' => empty($data['systemEnsalementSlots']) ? [
                new NotBlank(['message' => 'NotBlank.default']),
            ] : [],
            'mapped' => false,
        ]);

        $form->add('time', ! empty($data['date']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'required' => ! empty($data['date']) ? true : false,
                'attr' => [
                    'disabled' => ! empty($data['date']) ? false : true,
                ],
                'constraints' => ! empty($data['date']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'mapped' => false,
            ] + (! empty($data['date']) ? [
                'class' => SystemEnsalementSessions::class,
                'query_builder' => function (SystemEnsalementSessionsRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                    if (null !== $form->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $form->getData()->getEdition()->getId()));
                    }

                    $qb->andWhere($er->replaceFieldAlias('date =:date'));
                    $qb->setParameter('date', $data['date']);

                    $qb->addOrderBy('sess.type', 'ASC');

                    $qb->addOrderBy($er->replaceFieldAlias('date'), 'ASC');
                    $qb->addOrderBy($er->replaceFieldAlias('start'), 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%s - %s | %s', $entity->getStart()->format('H:i'), $entity->getEnd()->format('H:i'), array_search($entity->getType(), SystemEnsalementSessions::SESSION_TYPES));
                },
            ] : [
                'choice_translation_domain' => 'messages',
            ]));

        $form->add('article', ! empty($data['theme']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'attr' => [
                    'disabled' => ! empty($data['theme']) ? false : true,
                ],
                'constraints' => ! empty($data['theme']) ? [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'required' => false,
                'mapped' => false,
            ] + (! empty($data['theme']) ? [
                'class' => UserArticles::class,
                'query_builder' => function (UserArticlesRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));

                    if (null !== $form->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('editionId'), $form->getData()->getEdition()->getId()));
                    }

                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), 2));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('divisionId'), $data['division']));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('userThemes'), $data['theme']));

                    $qb->addOrderBy($er->replaceFieldAlias('title'), 'ASC');

                    return $qb;
                },
            ] : [
                'choice_translation_domain' => 'messages',
            ]));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Scheduling::class,
            'edition' => null,
        ]);
    }
}

