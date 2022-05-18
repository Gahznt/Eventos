<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
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
 * Class ThesisSearchType
 *
 * @package App\Bundle\Base\Form
 */
class ThesisSearchType extends AbstractType
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
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'search';
    }

    /**
     * @param FormBuilderInterface $form
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
        /** @var Thesis $entity */
        $entity = $event->getData();

        $data = [];

        if ($entity && $entity->getDivision()) {
            $data['division'] = $entity->getDivision()->getId();
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
        $options = $this->getOptions();

        $form
            ->add('division', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'translation_domain' => 'messages',
                'query_builder' => function (DivisionRepository $er) use ($form, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('id'), $ids));
                        }
                    }

                    $qb->addOrderBy($er->replaceFieldAlias('id'), 'ASC');

                    return $qb;
                },
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('theme', EntityType::class, [
                'class' => UserThemes::class,
                'attr' => [
                    //'disabled' => ! empty($data['division']) ? false : true,
                ],
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));

                    if (! empty($data['division'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['division']));
                    } else {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), 0));
                    }

                    if (! empty($options['edition'])) {
                        /** @var Edition $edition */
                        $edition = $options['edition'];

                        if (count($edition->getEvent()->getDivisions()) > 0) {
                            $ids = [];
                            foreach ($edition->getEvent()->getDivisions() as $item) {
                                $ids[] = $item->getId();
                            }

                            $qb->andWhere($qb->expr()->in($er->replaceFieldAlias('division'), $ids));
                        }
                    }

                    $qb->addOrderBy('ut.position', 'ASC');
                    $qb->addOrderBy('utd.portugueseTitle', 'ASC');

                    return $qb;
                },
                'choice_label' => function ($entity) {
                    return sprintf('%d - %s', $entity->getPosition(), $entity->getDetails()->getTitle());
                },
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choice_translation_domain' => 'messages',
                'choices' => [
                        'Todos' => '',
                    ] + Thesis::EVALUATION_STATUS,
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])
            ->add('q', TextType::class, [
                'required' => false,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Thesis::class,
            'edition' => null,
        ]);
    }
}