<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Repository\ActivityRepository;
use App\Bundle\Base\Repository\PanelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnsalementPriorityType extends EnsalementBaseType
{
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        // $data = $event->getData();

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
        if (! isset($data['division'])) {
            $data['division'] = null;
        }
        if (! isset($data['activityType'])) {
            $data['activityType'] = '';
        }
        if (! isset($data['activity'])) {
            $data['activity'] = null;
        }
        if (! isset($data['panel'])) {
            $data['panel'] = null;
        }

        $form->add('activityType', ChoiceType::class, [
            'placeholder' => 'Select',
            'choice_translation_domain' => 'messages',
            'required' => ! empty($data['division']) > 0 ? true : false,
            'mapped' => false,
            'choices' => ! empty($data['division']) > 0 ? (Activity::ACTIVITY_TYPES + ['Painel' => 'panel']) : [],
            'attr' => [
                'disabled' => ! empty($data['division']) > 0 ? false : true,
            ],
            'constraints' => [
                new NotBlank(['message' => 'NotBlank.default']),
            ],
        ]);

        if ($form->has('activity')) {
            $form->remove('activity');
        }

        if ($form->has('panel')) {
            $form->remove('panel');
        }

        if ('panel' === $data['activityType']) {
            $form->add('panel', EntityType::class, [
                'placeholder' => 'Select',
                'required' => true,
                'mapped' => true,
                'attr' => [
                    'disabled' => false,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'class' => Panel::class,
                'query_builder' => function (PanelRepository $er) use ($form, $data) {
                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('statusEvaluation'), 2)); // somente aprovados

                    if (null !== $form->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('editionId'), $form->getData()->getEdition()->getId()));
                    }

                    if (! empty($data['division'])) {
                        $qb->andWhere($er->replaceFieldAlias('divisionId =:division'));
                        $qb->setParameter('division', $data['division']);
                    }

                    return $qb;
                },
            ]);
        } else {
            $form->add('activity', strlen($data['activityType']) > 0 ? EntityType::class : ChoiceType::class, [
                    'placeholder' => 'Select',
                    'required' => strlen($data['activityType']) > 0 ? true : false,
                    'mapped' => true,
                    'attr' => [
                        'disabled' => strlen($data['activityType']) > 0 ? false : true,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'NotBlank.default']),
                    ],
                ] + (strlen($data['activityType']) > 0 ? [
                    'class' => Activity::class,
                    'query_builder' => function (ActivityRepository $er) use ($form, $data) {
                        $qb = $er->createQueryBuilder($er->getAlias());

                        if (null !== $form->getData()->getEdition()) {
                            $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $form->getData()->getEdition()->getId()));
                        }

                        if (! empty($data['division'])) {
                            $qb->andWhere($er->replaceFieldAlias('division =:division'));
                            $qb->setParameter('division', $data['division']);
                        }

                        if (strlen($data['activityType']) > 0) {
                            $qb->andWhere($er->replaceFieldAlias('activityType =:activityType'));
                            $qb->setParameter('activityType', $data['activityType']);
                        }

                        return $qb;
                    },
                ] : [
                    'choice_translation_domain' => 'messages',
                ]));
        }

        if (! empty($data['activity']) || ! empty($data['panel'])) {
            if (! empty($data['activity'])) {
                $form->add('timeRestriction', TextType::class, [
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'value' => ! empty($data['activity']) ? $this->em->getRepository(Activity::class)->find($data['activity'])->getTimeRestriction() : '',
                        'disabled' => 1,
                    ],
                    'constraints' => [
                        // new NotBlank(['message' => 'NotBlank.default']),
                    ],
                ]);
            }

            $this->addSlotsElements($form, $data);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => SystemEnsalementScheduling::class,
        ]);
    }
}

