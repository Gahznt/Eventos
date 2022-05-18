<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Repository\UserArticlesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CertificateAwardsType
 *
 * @package App\Bundle\Base\Form
 */
class CertificateAwardsType extends AbstractType
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
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => [
                    'Indicados à Premiação' => Certificate::CERTIFICATE_TYPE_INDICADOS_PREMIACAO,
                    'Premiados' => Certificate::CERTIFICATE_TYPE_PREMIADOS,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'required' => false,
            ])
            ->add('userArticles', EntityType::class, [
                'class' => UserArticles::class,
                'placeholder' => 'Select',
                'query_builder' => function (UserArticlesRepository $er) use ($form, $options) {

                    $qb = $er->createQueryBuilder('ua');

                    $qb->andwhere('ua.status = :status')->setParameter(':status', UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED)
                        ->andWhere('ua.editionId = :edition')->setParameter(':edition', $options['edition']->getid());

                    return $qb;
                },
                'constraints' => [
                    new NotBlank(['message' => 'NotBlank.default']),
                ],
                'mapped' => false,
                'required' => false,
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
        ]);
    }
}
