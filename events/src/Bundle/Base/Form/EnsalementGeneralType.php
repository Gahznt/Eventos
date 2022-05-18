<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Repository\SystemEnsalementSlotsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnsalementGeneralType extends AbstractType
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
            ->add('systemEnsalementSlots', EntityType::class, [
                'placeholder' => 'Select',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'mapped' => true,
                'class' => SystemEnsalementSlots::class,
                'query_builder' => function (SystemEnsalementSlotsRepository $er) use ($builder) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
                    $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

                    $qb->andWhere($qb->expr()->isNull($er->replaceFieldAlias('deletedAt')));
                    if (null !== $builder->getData() && null !== $builder->getData()->getEdition()) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $builder->getData()->getEdition()->getId()));
                    }

                    $qb->addOrderBy('sess.date', 'ASC');
                    $qb->addOrderBy('sess.start', 'ASC');

                    $qb->addOrderBy('sess.type', 'ASC');

                    $qb->addOrderBy('ser.name', 'ASC');
                    $qb->addOrderBy('ser.local', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%s | %s - %s | %s | %s', $entity->getSystemEnsalementSessions()->getDate()->format('d/m/Y'), $entity->getSystemEnsalementSessions()->getStart()->format('H:i'), $entity->getSystemEnsalementSessions()->getEnd()->format('H:i'), $entity->getSystemEnsalementRooms()->getName(), $entity->getSystemEnsalementRooms()->getLocal());
                },
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEnsalementScheduling::class,
        ]);
    }
}
