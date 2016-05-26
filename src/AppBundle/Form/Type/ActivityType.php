<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('time');
        $builder->add('distance');
        $builder->add('day', DateType::class, [
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Activity::class);
        $resolver->setDefault('allow_extra_fields', true);
    }
}