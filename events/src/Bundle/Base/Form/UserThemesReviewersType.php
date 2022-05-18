<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\UserThemesReviewers;
use App\Bundle\Base\Services\Util as UtilService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserThemesReviewersType
 *
 * @package App\Bundle\Base\Form
 */
class UserThemesReviewersType extends AbstractType
{
    private $utilService;

    public function __construct(UtilService $utilService)
    {
        $this->utilService = $utilService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-3'],
                    ]),
                ],
            ])
            ->add('linkLattes', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-3'],
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'NotBlank.default',
                        'groups' => ['theme-submission-step-3'],
                    ]),
                ],
            ])
            ->add('phone', NumberType::class)
            ->add('cellphone', NumberType::class)
            ->add('institute', TextType::class)
            ->add('program', TextType::class)
            ->add('state', TextType::class);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $whiteListInt = ['phone', 'cellphone'];
        $this->utilService->onlyIntInputs($whiteListInt, $data);
        $event->setData($data);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserThemesReviewers::class,
        ]);
    }
}
