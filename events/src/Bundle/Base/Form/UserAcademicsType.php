<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAcademics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserAcademicsType
 *
 * @package App\Bundle\Base\Form
 */
class UserAcademicsType extends AbstractType
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
        $form
            ->add('level', ChoiceType::class, [
                'choices' => USER::USER_LEVELS,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choice_value' => function ($value) {
                    return (int)$value;
                },
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],

            ])
            ->add('status', ChoiceType::class, [
                'choices' => User::USER_ACADEMIC_STATUS,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choice_value' => function ($value) {
                    return (int)$value;
                },
                'constraints' => self::$validationEnabled ? [
                    new NotBlank(['message' => 'NotBlank.default']),
                ] : [],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAcademics::class,
        ]);
    }
}
