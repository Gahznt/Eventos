<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\SystemEnsalementSchedulingArticles;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnsalementSectionType extends EnsalementBaseType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * EnsalementPriorityType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->setOptions($options);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var SystemEnsalementScheduling $entity */
        $entity = $event->getData();

        $data = [];

        if ($entity && $entity->getId()) {
            $data['id'] = $entity->getId();
        }

        if (
            $entity->getSystemEnsalementSlots()
            && $entity->getSystemEnsalementSlots()->getSystemEnsalementSessions()
        ) {
            $data['date'] = $entity->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getDate()->format('Y-m-d');
            $data['systemEnsalementSlots'] = $entity->getSystemEnsalementSlots()->getId();

            $this->addElements($form, $data);

            // não é mapeado - é usado para filtrar os slots
            $form->get('date')->setData($entity->getSystemEnsalementSlots()->getSystemEnsalementSessions());
        } else {
            $this->addElements($form, $data);
        }
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (! empty($data['systemEnsalementSlots'])) {
            $er = $this->em->getRepository(SystemEnsalementSlots::class);
            /** @var SystemEnsalementSlots $slot */
            $slot = $er->find($data['systemEnsalementSlots']);
            if ($slot) {
                $data['date'] = $slot->getSystemEnsalementSessions()->getDate()->format('Y-m-d');
                $data['systemEnsalementSessions'] = $slot->getSystemEnsalementSessions()->getId();
                $event->setData($data);
            }
        }

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
            $data['division'] = $form->getData() && $form->getData()->getDivision() ? $form->getData()->getDivision()->getId() : null;
        }
        if (! isset($data['userThemes'])) {
            $data['userThemes'] = $form->getData() && $form->getData()->getUserThemes() ? $form->getData()->getUserThemes()->getId() : null;
        }

        $form
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'translation_domain' => 'messages',
                'query_builder' => function (DivisionRepository $er) use ($form, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('id'), $ids));
                        }
                    }

                    $qb->addOrderBy($er->replaceFieldAlias('id'), 'ASC');

                    return $qb;
                },
            ])
            ->add('userThemes', EntityType::class, [
                'class' => UserThemes::class,
                'attr' => [
                    'disabled' => ! empty($data['division']) ? false : true,
                ],
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
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('format', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => SystemEnsalementScheduling::SECTION_FORMATS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => SystemEnsalementScheduling::LANGUAGES,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('coordinatorDebater1Type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => SystemEnsalementScheduling::COORDINATOR_DEBATER_TYPES,
            ])
            ->add('coordinatorDebater1', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'query_builder' => function (UserRepository $er) use ($form, $data, $options) {
                    $qb = $er->createQueryBuilder($er->getAlias());
                    //$qb->innerJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userAuthor=u.id');
                    //$qb->innerJoin(UserArticles::class, 'ua', 'WITH', 'ua.id=uaa.userArticles');

                    $qb->innerJoin(EditionSignup::class, 'esup', 'WITH', 'esup.joined=u.id');

                    $qb->leftJoin(EditionPaymentMode::class, 'epm', 'WITH', 'epm.id=esup.paymentMode');

                    if (! empty($options['edition'])) {
                        //$qb->andWhere($qb->expr()->eq('ua.editionId', $options['edition']));
                        $qb->andWhere($qb->expr()->eq('esup.edition', $options['edition']->getId()));
                    }

                    /*$qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID),
                            $qb->expr()->eq('epm.initials', $qb->expr()->literal(EditionPaymentMode::INITIALS['Ouvinte']))
                        )
                    );*/

                    $qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));
                    $qb->andWhere($qb->expr()->neq('epm.initials', $qb->expr()->literal(EditionPaymentMode::INITIALS['Ouvinte'])));

                    // $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
                    $qb->andWhere($qb->expr()->isNull('esup.deletedAt'));
                    $qb->andWhere($qb->expr()->isNull('u.deletedAt'));

                    $qb->addGroupBy('u.id');

                    $qb->addOrderBy('u.name', 'ASC');

                    return $qb;
                },
                'choice_label' => function (User $entity) {
                    return $entity->getName();
                },
                'required' => true,
                'mapped' => true,
            ])
            ->add('coordinatorDebater2Type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'choices' => SystemEnsalementScheduling::COORDINATOR_DEBATER_TYPES,
            ])
            ->add('coordinatorDebater2', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select',
                'query_builder' => function (UserRepository $er) use ($form, $data, $options) {
                    $qb = $er->createQueryBuilder($er->getAlias());
                    //$qb->innerJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userAuthor=u.id');
                    //$qb->innerJoin(UserArticles::class, 'ua', 'WITH', 'ua.id=uaa.userArticles');

                    $qb->innerJoin(EditionSignup::class, 'esup', 'WITH', 'esup.joined=u.id');

                    $qb->leftJoin(EditionPaymentMode::class, 'epm', 'WITH', 'epm.id=esup.paymentMode');

                    if (! empty($options['edition'])) {
                        //$qb->andWhere($qb->expr()->eq('ua.editionId', $options['edition']));
                        $qb->andWhere($qb->expr()->eq('esup.edition', $options['edition']->getId()));
                    }

                    /*$qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID),
                            $qb->expr()->eq('epm.initials', $qb->expr()->literal(EditionPaymentMode::INITIALS['Ouvinte']))
                        )
                    );*/

                    $qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));
                    $qb->andWhere($qb->expr()->neq('epm.initials', $qb->expr()->literal(EditionPaymentMode::INITIALS['Ouvinte'])));

                    // $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
                    $qb->andWhere($qb->expr()->isNull('esup.deletedAt'));
                    $qb->andWhere($qb->expr()->isNull('u.deletedAt'));

                    $qb->addGroupBy('u.id');

                    $qb->addOrderBy('u.name', 'ASC');

                    return $qb;
                },
                'choice_label' => function (User $entity) {
                    return $entity->getName();
                },
                'choice_translation_domain' => 'messages',
                'required' => false,
                'mapped' => true,
            ])
            // A collection é montada dinamicamente a partir dos itens selecionados em _article
            ->add('articles', CollectionType::class, [
                'entry_type' => EnsalementSectionArticlesType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Count([
                        'min' => 1,
                    ]),
                ],
            ])
            // Utilizado para listar os artigos que serão incluídos na collection articles
            ->add('_article', ! empty($data['userThemes']) ? EntityType::class : ChoiceType::class, [
                    'placeholder' => 'Select',
                    'attr' => [
                        'disabled' => ! empty($data['userThemes']) ? false : true,
                    ],
                    'constraints' => ! empty($data['userThemes']) ? [
                        // new NotBlank(['message' => 'NotBlank.default']),
                    ] : [],
                    'required' => false,
                    'mapped' => false,
                ] + (! empty($data['userThemes']) ? [
                    'class' => UserArticles::class,
                    'query_builder' => function (UserArticlesRepository $er) use ($form, $data) {

                        $qb = $er->createQueryBuilder($er->getAlias());

                        $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));

                        if (null !== $form->getData()->getEdition()) {
                            $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('editionId'), $form->getData()->getEdition()->getId()));
                        }

                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED));
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('divisionId'), $data['division']));
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('userThemes'), $data['userThemes']));

                        $in = $er->createQueryBuilder('ua2');
                        $in->select('ua2.id');
                        $in->innerJoin(SystemEnsalementSchedulingArticles::class, 'sesa', 'WITH', 'sesa.userArticles=ua2.id');
                        $in->innerJoin('sesa.systemEnsalementSheduling', 'sesss');

                        $in->andWhere($qb->expr()->isNull('ua2.deletedAt'));
                        $in->andWhere($qb->expr()->isNull('sesa.deletedAt'));
                        $in->andWhere($qb->expr()->isNull('sesss.deletedAt'));

                        if (! empty($data['id'])) {
                            $in->andWhere($qb->expr()->neq('sesss.id', (int)$data['id']));
                        }

                        $qb->andWhere($qb->expr()->notIn($er->replaceFieldAlias('id'), $in->getDQL()));

                        $qb->addOrderBy($er->replaceFieldAlias('title'), 'ASC');

                        return $qb;
                    },
                ] : [
                    'choice_translation_domain' => 'messages',
                ]));

        $this->addSlotsElements($form, $data);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementScheduling::class,
            'edition' => null,
        ]);
    }
}

