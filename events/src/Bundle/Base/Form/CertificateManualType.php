<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CertificateManualType
 *
 * @package App\Bundle\Base\Form
 */
class CertificateManualType extends AbstractType
{
    /**
     * @var array
     */
    protected array $options = [];

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

    /**
     * @param FormBuilderInterface $form
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $form, array $options)
    {
        if(empty($options['user'])){
            $options['user'] = [0];
        }
        $this->setOptions($options);

        $form->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
        $form->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var Thesis $entity */
        $entity = $event->getData();

        $data = [];

        /*if ($entity && $entity->getDivision()) {
            $data['division'] = $entity->getDivision()->getId();
        }*/

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
        $options = $this->getOptions();

        $form
            ->add('user', EntityType::class, [
                'class' => User::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'query_builder' => function (UserRepository $er) use ($form, $data, $options) {
                    $qb = $er->createQueryBuilder('u');

                    return $qb->where(
                        $qb->expr()->in('u.id', $options['user'])
                    );
                },
                'choice_label' => function (User $entity) {
                    return $entity->getName();
                },
                'required' => false,
                'multiple' => true,
                'mapped' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                    ]),
                ],
            ])
            ->add('title', TextType::class, [
                'constraints' => [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 5,
                ],
                'constraints' => [
                    // new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
                'mapped' => false,
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'rows' => 10,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
                'mapped' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Certificate::class,
            'edition' => null,
            'user' => [],
        ]);
    }
}
