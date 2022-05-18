<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Repository\SystemEnsalementSessionsRepository;
use App\Bundle\Base\Repository\SystemEnsalementSlotsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnsalementBaseType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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

        $this->addSlotsElements($form);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->addSlotsElements($form, $data);
    }

    /**
     * @param FormInterface $form
     * @param array $data
     */
    protected function addSlotsElements(FormInterface $form, $data = [])
    {
        if (! isset($data['date'])) {
            $data['date'] = '';
        }
        if (! isset($data['systemEnsalementSessions'])) {
            $data['systemEnsalementSessions'] = '';
        }
        if (! isset($data['systemEnsalementSlots'])) {
            $data['systemEnsalementSlots'] = '';
        }

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

        $form->add('systemEnsalementSessions', ! empty($data['date']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'required' => ! empty($data['date']) ? true : false,
                'attr' => [
                    'disabled' => ! empty($data['date']) ? false : true,
                ],
                'constraints' => ! empty($data['date']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                // 'mapped' => false,
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

        $form->add('systemEnsalementSlots', ! empty($data['systemEnsalementSessions']) || ! empty($data['systemEnsalementSlots']) ? EntityType::class : ChoiceType::class, [
                'placeholder' => 'Select',
                'required' => ! empty($data['systemEnsalementSessions']) || ! empty($data['systemEnsalementSlots']) ? true : false,
                'attr' => [
                    'disabled' => ! empty($data['systemEnsalementSessions']) || ! empty($data['systemEnsalementSlots']) ? false : true,
                ],
                'constraints' => ! empty($data['systemEnsalementSessions']) || ! empty($data['systemEnsalementSlots']) ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
                'mapped' => true,
            ] + (! empty($data['systemEnsalementSessions']) || ! empty($data['systemEnsalementSlots']) ? [
                'class' => SystemEnsalementSlots::class,
                'query_builder' => function (SystemEnsalementSlotsRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
                    $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                    if (null !== $form->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $form->getData()->getEdition()->getId()));
                    }

                    if (! empty($data['systemEnsalementSessions'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('systemEnsalementSessions'), $data['systemEnsalementSessions']));
                    }

                    $qb->addOrderBy('sess.date', 'ASC');
                    $qb->addOrderBy('sess.start', 'ASC');

                    $qb->addOrderBy('sess.type', 'ASC');

                    $qb->addOrderBy('ser.name', 'ASC');
                    $qb->addOrderBy('ser.local', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%s | %s - %s | %s | %s | %s', $entity->getSystemEnsalementSessions()->getDate()->format('d/m/Y'), $entity->getSystemEnsalementSessions()->getStart()->format('H:i'), $entity->getSystemEnsalementSessions()->getEnd()->format('H:i'), array_search($entity->getSystemEnsalementSessions()->getType(), SystemEnsalementSessions::SESSION_TYPES), $entity->getSystemEnsalementRooms()->getName(), $entity->getSystemEnsalementRooms()->getLocal());
                },
            ] : [
                'choice_translation_domain' => 'messages',
            ]));
    }
}
