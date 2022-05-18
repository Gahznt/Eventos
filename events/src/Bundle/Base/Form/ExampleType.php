<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Example;
use App\Bundle\Base\Entity\SubDependentExample;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use App\Bundle\Base\Services\SubDependentExample as SubDependentExampleService;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ExampleType
 * @package App\Bundle\Base\Form
 */
class ExampleType extends AbstractType
{
    private $subDependentService;

    public function __construct(SubDependentExampleService $subDependentExampleService)
    {
        $this->subDependentService = $subDependentExampleService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('dependentExample', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\DependentExample',
                'required' => true,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false
            ])
            ->add('subDependentExample', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\subDependentExample',
                'required' => true,
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'mapped' => false,
                'choices' => []
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData']);
    }

    protected function addElements(FormInterface $form, ?SubDependentExample $subDependentExample = null)
    {
        $form->add('subDependentExample', EntityType::class, [
            'required' => true,
            'placeholder' => 'Select ...',
            'data' => $subDependentExample,
            'class' => 'App\Bundle\Base\Entity\SubDependentExample'
        ]);
    }

    function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $subDependentExample = $data->getSubDependentExample();

        if ($subDependentExample) {
            $form->get('dependentExample')->setData($subDependentExample->getDependentExample());
            $form->add('subDependentExample', EntityType::class, [
                'class' => 'App\Bundle\Base\Entity\SubDependentExample',
                'placeholder' => 'Select',
                'choice_translation_domain' => 'messages',
                'choices' => $subDependentExample->getDependentExample()->getSubDependents()
            ]);
        }
    }

    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $dependent = $this->subDependentService->getSubDependentExample($data['dependentExample']);

        $this->addElements($form, $dependent);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Example::class,
        ]);
    }
}