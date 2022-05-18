<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\SystemEvaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SystemEvaluationAuthorRateType extends AbstractType
{
    /**
     * @var int|null
     */
    public static ?int $blockPrefixIndex = 0;

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'systemEvaluationAuthorRate_' . self::$blockPrefixIndex;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
            ->add('authorRateOne', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => SystemEvaluation::AUTHOR_RATE_ONE_OPTIONS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'expanded' => true,
            ])
            ->add('authorRateTwo', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => SystemEvaluation::AUTHOR_RATE_TWO_OPTIONS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEvaluation::class,
        ]);
    }
}
