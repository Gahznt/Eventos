<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluationIndicationsSearch;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SystemEvaluationIndicationsSearchType
 *
 * @package App\Bundle\Base\Form
 */
class SystemEvaluationIndicationsSearchType extends AbstractType
{
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
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return '';
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
        $options = $this->getOptions();

        if (! isset($data['division'])) {
            $data['division'] = $form->getData() && $form->getData()->getDivision() ? $form->getData()->getDivision() : null;
        }

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

                    $qb->addOrderBy($er->replaceFieldAlias('portuguese'), 'ASC');

                    return $qb;
                },
            ])
            ->add('theme', EntityType::class, [
                'class' => UserThemes::class,
                'placeholder' => 'Select',
                'translation_domain' => 'messages',
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));
                    if (! empty($data['division'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['division']));
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
            ])
            ->add('level', ChoiceType::class, [
                'placeholder' => 'Selecione',
                'choices' => User::USER_LEVELS,
            ])
            ->add('search', TextType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['style' => 'display:none;'],
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SystemEvaluationIndicationsSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'systemEvaluationIndicationSearch'],
            'edition' => null,
        ]);
    }
}
