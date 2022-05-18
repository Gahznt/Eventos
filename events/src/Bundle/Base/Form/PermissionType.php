<?php

namespace App\Bundle\Base\Form;

use App\Bundle\Base\Entity\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Bundle\Base\Services\Helper\Permission as PermissionService;

/**
 * Class PermissionType
 * @package App\Bundle\Base\Form
 */
class PermissionType extends AbstractType
{
    private $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permissions', ChoiceType::class, [
                'placeholder' => 'Select',
                'choices' => $this->permissionService::getPermissions(),
                'choice_translation_domain' => 'messages'
            ])
            ->add('search', TextType::class, [
                'required' => false
            ])
            ->add('levels', ChoiceType::class, [
                'placeholder' => 'Select',
                'choices' => $this->permissionService::getLevels(),
                'choice_translation_domain' => 'messages'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['style' => 'display:none;']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Permission::class,
            'method' => 'get',
            'csrf_protection' => false,
            'attr' => ['class' => 'row', 'novalidate' => 'novalidate', 'id' => 'permissionFormSearch'],
        ]);
    }
}