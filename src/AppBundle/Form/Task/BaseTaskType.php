<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 02.04.17
 * Time: 16:49
 */

namespace AppBundle\Form\Task;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BaseTaskType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ]
            ])
            ->add('end_date', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom:15px;'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
                    'style' => 'margin-bottom:15px'
                ]
            ]);
    }
}