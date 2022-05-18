<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Repository\InstitutionRepository;
use App\Bundle\Base\Repository\ProgramRepository;
use App\Bundle\Base\Repository\StateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserInstitutionsProgramsType
 *
 * @package App\Bundle\Base\Form
 */
class UserInstitutionsProgramsType extends AbstractType
{
    /**
     * @var bool
     */
    public static $validationEnabled = true;

    public function buildForm(FormBuilderInterface $form, array $options)
    {
        $form->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $form->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var UserInstitutionsPrograms $entity */
        $entity = $event->getData();

        $data = [];

        if ($entity && $entity->getInstitutionFirstId()) {
            $data['institutionFirstId'] = $entity->getInstitutionFirstId()->getId();
        }

        if ($entity && $entity->getInstitutionSecondId()) {
            $data['institutionSecondId'] = $entity->getInstitutionSecondId()->getId();
        }

        $this->addElements($form, $data);
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
        $form
            ->add('institutionFirstId', EntityType::class, [
                'required' => true,
                'class' => Institution::class,
                'query_builder' => function (InstitutionRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());                    

                    $qb->andWhere($qb->expr()->isNull('i.deletedAt'));
                    $qb->andWhere($qb->expr()->eq('i.status', 1));

                    return $qb;
                },                
                'choice_label' => function ($qb) {
                    return  empty($qb->getInitials()) ? $qb->getName() : $qb->getInitials() . ' - ' . $qb->getName(); 
                },
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ])
            ->add('otherInstitutionFirst', TextType::class, [
                'required' => false,
            ])
            ->add('programFirstId', EntityType::class, [
                'required' => true,
                'placeholder' => 'Select',
                'attr' => [],
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],

                'class' => Program::class,
                'query_builder' => function (ProgramRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->andWhere($qb->expr()->isNull('p.deletedAt'));
                    $qb->andWhere($qb->expr()->eq('p.status', 1));

                    if (! empty($data['institutionFirstId'])) {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->eq($er->replaceFieldAlias('institution'), (int)$data['institutionFirstId']),
                            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
                        ));
                    } else {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->eq($er->replaceFieldAlias('institution'), 0),
                            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
                        ));
                    }

                    return $qb;
                },
                'choice_label' => function (Program $entity) {
                    return $entity->getName();
                },
            ])
            ->add('otherProgramFirst', TextType::class, [
                'required' => false,
            ])
            ->add('institutionSecondId', EntityType::class, [
                'required' => false,
                'class' => Institution::class,
                'query_builder' => function (InstitutionRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->andWhere($qb->expr()->isNull('i.deletedAt'));
                    $qb->andWhere($qb->expr()->eq('i.status', 1));

                    return $qb;
                },
                'choice_label' => function ($qb) {
                    return  empty($qb->getInitials()) ? $qb->getName() : $qb->getInitials() . ' - ' . $qb->getName(); 
                },                
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('otherInstitutionSecond', TextType::class, [
                'required' => false,
            ])
            ->add('programSecondId', EntityType::class, [
                'required' => false,
                'placeholder' => 'Select',
                'attr' => [],
                'constraints' => [],

                'class' => Program::class,
                'query_builder' => function (ProgramRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->andWhere($qb->expr()->isNull('p.deletedAt'));
                    $qb->andWhere($qb->expr()->eq('p.status', 1));

                    if (! empty($data['institutionSecondId'])) {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->eq($er->replaceFieldAlias('institution'), (int)$data['institutionSecondId']),
                            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
                        ));
                    } else {
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->eq($er->replaceFieldAlias('institution'), 0),
                            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
                        ));
                    }

                    return $qb;
                },
                'choice_label' => function (Program $entity) {
                    return $entity->getName();
                },
                'choice_translation_domain' => 'messages',
            ])
            ->add('otherProgramSecond', TextType::class, [
                'required' => false,
            ])
            ->add('stateFirstId', EntityType::class, [
                'required' => false,
                'class' => State::class,
                'query_builder' => function (StateRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('country'), Country::DEFAULT_LOCATE_ID));
                    $qb->orWhere($qb->expr()->eq($er->replaceFieldAlias('country'), Country::OTHER_LOCATE_ID));
                    $qb->addOrderBy($er->replaceFieldAlias('country'), 'asc');
                    $qb->addOrderBy($er->replaceFieldAlias('name'), 'asc');

                    return $qb;
                },
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ])
            ->add('stateSecondId', EntityType::class, [
                'required' => false,
                'class' => State::class,
                'query_builder' => function (StateRepository $er) use ($form, $data) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('country'), Country::DEFAULT_LOCATE_ID));
                    $qb->orWhere($qb->expr()->eq($er->replaceFieldAlias('country'), Country::OTHER_LOCATE_ID));
                    $qb->addOrderBy($er->replaceFieldAlias('country'), 'asc');
                    $qb->addOrderBy($er->replaceFieldAlias('name'), 'asc');

                    return $qb;
                },
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserInstitutionsPrograms::class,
        ]);
    }
}
