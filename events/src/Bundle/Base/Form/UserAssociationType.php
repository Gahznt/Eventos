<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Repository\ProgramRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserAssociationType
 *
 * @package App\Bundle\Base\Form
 */
class UserAssociationType extends AbstractType
{
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
        if (! isset($data['institution'])) {
            $data['institution'] = $form->getData() && $form->getData()->getInstitution() ? $form->getData()->getInstitution()->getId() : null;
        }

        $form
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => UserAssociation::USER_ASSOCIATIONS_TYPE,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('institution', EntityType::class, [
                'class' => Institution::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('otherInstitution', TextType::class)
            ->add('program', ! empty($data['institution']) ? EntityType::class : ChoiceType::class, [
                    'placeholder' => 'Select',
                    'attr' => [
                        //'disabled' => ! empty($data['institution']) ? false : true,
                    ],
                    'constraints' => ! empty($data['institution']) ? [
                        new NotBlank(['message' => 'NotBlank.default']),
                    ] : [],
                    'required' => false,
                    'mapped' => true,
                ] + (! empty($data['institution']) ? [
                    'class' => Program::class,
                    'query_builder' => function (ProgramRepository $er) use ($form, $data) {

                        $qb = $er->createQueryBuilder($er->getAlias());
                        $qb->andWhere($qb->expr()->orX(
                            $qb->expr()->eq($er->replaceFieldAlias('institution'), $data['institution']),
                            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
                        ));

                        return $qb;
                    },
                    'choice_label' => function (Program $entity) {
                        return $entity->getName();
                    },
                ] : [
                    'choice_translation_domain' => 'messages',
                ]))
            ->add('otherProgram', TextType::class)
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                //'choice_translation_domain' => 'messages',
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('aditionals', EntityType::class, [
                'class' => Division::class,
                //'placeholder' => 'Select',
                'label' => false,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAssociation::class,
        ]);
    }
}
