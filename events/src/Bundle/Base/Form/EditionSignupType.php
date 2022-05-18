<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Repository\EditionPaymentModeRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditionSignupType
 *
 * @package App\Bundle\Base\Form
 */
class EditionSignupType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $options = [];

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
     * EnsalementGeneralSearchType constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $form, array $options)
    {
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
        $isFreeIndividualAssociationDivisionRequired = false;

        if (! empty($data['paymentMode']) && ! empty($data['wantFreeIndividualAssociation'])) {
            /** @var EditionPaymentMode $paymentMode */
            $paymentMode = $this->em->getRepository(EditionPaymentMode::class)->find($data['paymentMode']);

            if ($paymentMode && $paymentMode->getHasFreeIndividualAssociation()) {
                $isFreeIndividualAssociationDivisionRequired = true;
            }
        }

        $options = $this->getOptions();

        $form
            ->add('badge', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('initialInstitute', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                // 'required' => empty($builder->getData()->getId()),
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                    ]),
                ],
            ])
            ->add('paymentMode', EntityType::class, [
                'class' => EditionPaymentMode::class,
                'label' => false,
                'expanded' => true,
                'query_builder' => function (EditionPaymentModeRepository $er) use ($form, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    if (! empty($options['edition'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('edition'), $options['edition']->getId()));
                    }

                    if (isset($options['isAssociated']) && true === $options['isAssociated']) {
                        $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('type'), [EditionPaymentMode::TYPE_ASSOCIATED, EditionPaymentMode::TYPE_ALL]));
                    } else {
                        $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('type'), [EditionPaymentMode::TYPE_NOT_ASSOCIATED, EditionPaymentMode::TYPE_ALL]));
                    }

                    $qb->addOrderBy($er->replaceFieldAlias('value'), 'DESC');

                    return $qb;
                },
                'choice_label' => function (EditionPaymentMode $entity) use ($options) {
                    $value = $entity->getValue();

                    if (isset($options['discount']) && $options['discount'] > 0) {
                        $value = $value - ($value * $options['discount'] / 100);
                    }

                    return json_encode([
                        'label' => $entity->getName(),
                        'value' => $value,
                        'hasFreeIndividualAssociation' => (int)$entity->getHasFreeIndividualAssociation(),
                    ]);
                },
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('userArticles', EntityType::class, [
                'class' => UserArticles::class,
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (UserArticlesRepository $er) use ($form, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    $qb->leftJoin(userArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=ua.id')
                        ->andwhere('ua.status = :status')->setParameter(':status', UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED)
                        ->andWhere('ua.editionId = :edition')->setParameter(':edition', $options['edition']->getid());

                    if (! empty($options['user'])) {
                        $qb->andWhere('uaa.userAuthor = :user')->setParameter(':user', $options['user']->getId());
                    }

                    return $qb;
                },
            ])
            ->add('wantFreeIndividualAssociation', CheckboxType::class, [
                // 'required' => false,
            ])
            ->add('freeIndividualAssociationDivision', EntityType::class, [
                'class' => Division::class,
                // 'mapped' => false,
                'placeholder' => 'Select',
                //'translation_domain' => true,
                'constraints' => $isFreeIndividualAssociationDivisionRequired
                    ? [
                        new NotBlank(['message' => 'NotBlank.default']),
                    ]
                    : [],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EditionSignup::class,
            'edition' => null,
            'isAssociated' => false,
            'discount' => 0,
            'user' => null,
        ]);
    }
}
