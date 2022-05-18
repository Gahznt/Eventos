<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\UserEvaluationArticles;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserEvaluationArticlesType
 *
 * @package App\Bundle\Base\Form
 */
class UserEvaluationArticlesType extends AbstractType
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
     * @var UserThemesService
     */
    private $userThemesService;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * UserEvaluationArticlesType constructor.
     *
     * @param UserThemesService $userThemesService
     * @param SessionInterface $session
     */
    public function __construct(
        UserThemesService $userThemesService,
        SessionInterface  $session
    )
    {
        $this->userThemesService = $userThemesService;
        $this->session = $session;
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
     * @param FormInterface $form
     * @param array $data
     */
    protected function addElements(FormInterface $form, $data = [])
    {
        $options = $this->getOptions();

        $keywordsByLang = $this->userThemesService->getKeywordsByLang($this->session->get('_locale'));

        if (! isset($data['wantEvaluate'])) {
            $data['wantEvaluate'] = $form->getData() ? $form->getData()->getWantEvaluate() : false;
        }

        if (! isset($data['divisionFirstId'])) {
            $data['divisionFirstId'] = $form->getData() && $form->getData()->getDivisionFirstId() ? $form->getData()->getDivisionFirstId()->getId() : false;
        }

        if (! isset($data['divisionSecondId'])) {
            $data['divisionSecondId'] = $form->getData() && $form->getData()->getDivisionSecondId() ? $form->getData()->getDivisionSecondId()->getId() : false;
        }

        if (! isset($data['themeFirstId'])) {
            $data['themeFirstId'] = $form->getData() && $form->getData()->getThemeFirstId() ? $form->getData()->getThemeFirstId()->getId() : false;
        }

        if (! isset($data['themeSecondId'])) {
            $data['themeSecondId'] = $form->getData() && $form->getData()->getThemeSecondId() ? $form->getData()->getThemeSecondId()->getId() : false;
        }

        $keyrowdFirst = [];
        if (! empty($data['themeFirstId'])) {
            $lang = UserThemesService::$languages[$this->session->get('_locale', 'pt_br')];
            $keywords = $this->userThemesService->getKeywordsByThemeLang($data['themeFirstId'], $lang);
            foreach ($keywords as $keyword) {
                $keyrowdFirst[$keyword['id']] = $keyword['name'];
            }
            asort($keyrowdFirst);
        }

        $keyrowdSecond = [];
        if (! empty($data['themeSecondId'])) {
            $lang = UserThemesService::$languages[$this->session->get('_locale', 'pt_br')];
            $keywords = $this->userThemesService->getKeywordsByThemeLang($data['themeSecondId'], $lang);
            foreach ($keywords as $keyword) {
                $keyrowdSecond[$keyword['id']] = $keyword['name'];
            }
            asort($keyrowdSecond);
        }

        $form
            ->add('divisionFirstId', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => true,
                'constraints' => ! empty($data['wantEvaluate']) ? [
                    new NotBlank([
                        'message' => 'divisionFirst.not_blank',
                    ]),
                ] : [],
            ])
            ->add('divisionSecondId', EntityType::class, [
                'class' => Division::class,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => true,
                'constraints' => ! empty($data['wantEvaluate']) ? [
                    new NotBlank([
                        'message' => 'divisionSecond.not_blank',
                    ]),
                ] : [],
            ])
            ->add('themeFirstId', EntityType::class, [
                'class' => UserThemes::class,
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));
                    if (! empty($data['divisionFirstId'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['divisionFirstId']));
                    } else {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('id'), 0));
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
                'constraints' => ! empty($data['wantEvaluate']) ? [
                    new NotBlank([
                        'message' => 'theme.not_blank',
                    ]),
                ] : [],
            ])
            ->add('themeSecondId', EntityType::class, [
                'class' => UserThemes::class,
                'query_builder' => function (UserThemesRepository $er) use ($form, $data, $options) {

                    $qb = $er->createQueryBuilder($er->getAlias());
                    $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');
                    $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));
                    $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('status'), UserThemes::THEME_EVALUATION_APPROVED));
                    if (! empty($data['divisionSecondId'])) {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('division'), $data['divisionSecondId']));
                    } else {
                        $qb->andWhere($qb->expr()->eq($er->replaceFieldAlias('id'), 0));
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
                'constraints' => ! empty($data['wantEvaluate']) ? [
                    new NotBlank([
                        'message' => 'theme.not_blank',
                    ]),
                ] : [],
            ])
            ->add('portuguese', CheckboxType::class, [
                'required' => false,
            ])
            ->add('english', CheckboxType::class, [
                'required' => false,
            ])
            ->add('spanish', CheckboxType::class, [
                'required' => false,
            ])
            ->add('wantEvaluate', CheckboxType::class, [
                'required' => false,
            ]);
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserEvaluationArticles::class,
            'edition' => null,
        ]);
    }
}
