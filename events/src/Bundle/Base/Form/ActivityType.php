<?php

namespace App\Bundle\Base\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ActivityType extends ActivityBaseType
{
    /**
     * @var int
     */
    public static $step = 1;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ActivityBaseType::buildForm($builder, $options);

        $builder
            ->add('panelists', CollectionType::class, [
                'entry_type' => ActivitiesPanelistsType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('guests', CollectionType::class, [
                'entry_type' => ActivitiesGuestsType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ]);

    }
}
