<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Subsection;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Repository\SystemEnsalementRoomsRepository;
use App\Bundle\Base\Repository\SystemEnsalementSessionsRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class SystemEnsalementSlotsType extends AbstractType
{
    /**
     * @var SystemEnsalementSessionsRepository
     */
    protected $ensalementSessionsRepository;

    /**
     * @var SystemEnsalementRoomsRepository
     */
    protected $ensalementRoomsRepository;

    /**
     * SystemEnsalementSlotsType constructor.
     *
     * @param SystemEnsalementSessionsRepository $ensalementSessionsRepository
     * @param SystemEnsalementRoomsRepository $ensalementRoomsRepository
     */
    public function __construct(SystemEnsalementSessionsRepository $ensalementSessionsRepository, SystemEnsalementRoomsRepository $ensalementRoomsRepository)
    {
        $this->ensalementSessionsRepository = $ensalementSessionsRepository;
        $this->ensalementRoomsRepository = $ensalementRoomsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', EntityType::class, [
                'class' => SystemEnsalementSessions::class,
                'query_builder' => function (SystemEnsalementSessionsRepository $er) use ($builder) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                    if (null !== $builder->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $builder->getData()->getEdition()->getId()));
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
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'mapped' => false,
            ])
            ->add('systemEnsalementRooms', EntityType::class, [
                'class' => SystemEnsalementRooms::class,
                'query_builder' => function (SystemEnsalementRoomsRepository $er) use ($builder) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));

                    if (null !== $builder->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $builder->getData()->getEdition()->getId()));
                    }

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%s | %s', $entity->getName(), $entity->getLocal());
                },
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (! empty($data['date'])) {
            $form->add('systemEnsalementSessions', EntityType::class, [
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
                'expanded' => true,
                'multiple' => true,
                'choice_translation_domain' => 'messages',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                    new Count([
                        'min' => 1,
                    ]),
                ],
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => SystemEnsalementSlots::class
        ]);
    }
}
